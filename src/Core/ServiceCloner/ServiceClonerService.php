<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use App\Core\ServiceCloner\Exception\NonExistingServiceException;
use App\Core\ServiceCloner\Exception\NonExistingServiceInstanceException;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerDeleteServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Docker\ContainerStopServiceInterface;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ServiceClonerService implements ServiceClonerServiceInterface
{
    private const MASTER_NAME = 'master';

    private ConfigurationServiceInterface $dockerConfiguration;

    private Filesystem $filesystem;

    private LoggerInterface $logger;

    private FilesystemServiceInterface $zfsService;

    private SluggerInterface $slugger;

    private ContainerCreationServiceInterface $containerStateService;

    private ServiceCloner $configuration;

    private ContainerStateServiceInterface $dockerStateService;

    private ServiceClonerLifeCycleHookServiceInterface $serviceClonerLifeCycleHookService;

    private ServiceClonerNamingServiceInterface $serviceClonerNamingService;

    private ServiceClonerStateService $serviceClonerStateService;

    private ContainerStopServiceInterface $containerStopService;

    private ContainerDeleteServiceInterface $containerDestroyService;

    private IndexManagerServiceInterface $indexManagerService;

    public function __construct(
        ConfigurationServiceInterface $dockerConfiguration,
        LoggerInterface $logger,
        Filesystem $filesystem,
        FilesystemServiceInterface $filesystemService,
        ContainerCreationServiceInterface $containerCreationService,
        ContainerStopServiceInterface $containerStopService,
        ContainerDeleteServiceInterface $containerDestroyService,
        ServiceClonerLifeCycleHookServiceInterface $serviceClonerLifeCycleHookService,
        ServiceClonerNamingServiceInterface $serviceClonerNamingService,
        ServiceClonerStateService $serviceClonerStateService,
        ContainerStateServiceInterface $containerStateService,
        IndexManagerServiceInterface $indexManagerService,
        SluggerInterface $slugger
    ) {
        $this->dockerConfiguration = $dockerConfiguration;
        $this->configuration = $this->dockerConfiguration->getConfiguration();
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->zfsService = $filesystemService;
        $this->slugger = $slugger;
        $this->containerStateService = $containerCreationService;
        $this->containerStopService = $containerStopService;
        $this->containerDestroyService = $containerDestroyService;
        $this->dockerStateService = $containerStateService;
        $this->serviceClonerLifeCycleHookService = $serviceClonerLifeCycleHookService;
        $this->serviceClonerNamingService = $serviceClonerNamingService;
        $this->indexManagerService = $indexManagerService;
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function start(string $masterName, string $instanceName, ?int $index): void
    {
        if ($index === null) {
            $index = $this->indexManagerService->getNextAvailable();
        }
        $this->logger->debug(sprintf('-------------%s----------', $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@')));
        $this->startFilesystem($masterName, $instanceName);
        $this->startDocker($masterName, $instanceName, $index);
        $this->serviceClonerStateService->saveState($masterName, $instanceName, $index);
    }

    public function stop(string $masterName, string $instanceName): void
    {
        $serviceState = $this->serviceClonerStateService->getState($masterName, $instanceName);
        if ($serviceState === null) {
            throw new NonExistingServiceInstanceException($masterName, $instanceName);
        }
        $containerName = $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '_');

        $containerParameter = new ContainerParameterDTO(
            $containerName,
            $serviceState->getIndex(),
            $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName)
        );

        $service = $this->configuration->getServiceByName($masterName);
        if ($service === null) {
            throw new NonExistingServiceException($masterName);
        }
        $this->containerStopService->stop($containerName);
        $this->containerDestroyService->delete($containerName);

        $this->serviceClonerLifeCycleHookService->postDestroy($service, $containerParameter);
        $this->stopFilesystem($masterName, $instanceName);

        $this->serviceClonerStateService->deleteState($masterName, $instanceName);
    }

    private function startDocker(string $masterName, string $instanceName, int $index): void
    {
        $containerName = $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '_');

        $containerParameter = new ContainerParameterDTO(
            $containerName,
            $index,
            $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName)
        );

        if ($this->dockerStateService->dockerState($containerName) === 'running') {
            return;
        }

        $service = $this->configuration->getServiceByName($masterName);
        if ($service === null) {
            throw new NonExistingServiceException($masterName);
        }

        $this->serviceClonerLifeCycleHookService->preStart($service, $containerParameter);
        $this->containerStateService->createDocker(
            $containerParameter,
            $service
        );
        $this->serviceClonerLifeCycleHookService->postStartWaiter($service, $containerParameter);
        $this->serviceClonerLifeCycleHookService->postStart($service, $containerParameter);
    }

    private function startFilesystem(string $masterName, string $instanceName): void
    {
        $zfsFilesystemPath = $this->serviceClonerNamingService->getZfsFilesystemName($masterName, $instanceName);

        if ($this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            return;
        }

        if ($instanceName === self::MASTER_NAME) {
            $this->zfsService->createFilesystem($zfsFilesystemPath);

            return;
        }

        if ($this->zfsService->isSnapshoted($zfsFilesystemPath)) {
            return;
        }

        $filesystem = sprintf(
            '%s/%s',
            $this->dockerConfiguration->getConfiguration()->getZpoolName(),
            $this->slugger->slug($masterName)->toString()
        );
        $snapshotName = $this->slugger->slug($instanceName)->toString();

        $this->zfsService->createSnapshot($filesystem, $snapshotName);
        $this->zfsService->cloneSnapshot($filesystem, $snapshotName);
    }

    private function stopFilesystem(string $masterName, string $instanceName): void
    {
        $zfsFilesystemName = $this->dockerConfiguration->getConfiguration()->getZpoolName() . '/' . $masterName;
        if (!$this->zfsService->hasSnapshot($zfsFilesystemName, $instanceName)) {
            $this->logger->debug(sprintf('Can not stop filesystem, !hasSnapshot %s', $instanceName));

            return;
        }
        $zfsFilesystemPath = $this->serviceClonerNamingService->getZfsFilesystempath($masterName, $instanceName);
        if (!$this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            return;
        }

        if (!$this->zfsService->isSnapshoted($zfsFilesystemName)) {
            $this->logger->debug(sprintf('Can not stop filesystem, !hasFilesystem %s', $zfsFilesystemPath));

            return;
        }

        if ($instanceName === self::MASTER_NAME && $this->zfsService->isSnapshoted($zfsFilesystemName)) {
            $this->logger->debug(sprintf('Can not stop filesystem, !isSnapshoted %s', $zfsFilesystemName));

            return;
        }

        $filesystem = sprintf(
            '%s/%s',
            $this->dockerConfiguration->getConfiguration()->getZpoolName(),
            $this->slugger->slug($masterName)->toString()
        );
        $snapshotName = $this->slugger->slug($instanceName)->toString();

        $this->zfsService->destroySnapshot($filesystem, $snapshotName);
    }
}
