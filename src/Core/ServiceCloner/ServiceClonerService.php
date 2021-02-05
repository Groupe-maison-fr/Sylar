<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use App\Core\ServiceCloner\Exception\NonExistingServiceException;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
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

    public function __construct(
        ConfigurationServiceInterface $dockerConfiguration,
        LoggerInterface $logger,
        Filesystem $filesystem,
        FilesystemServiceInterface $filesystemService,
        ContainerCreationServiceInterface $containerCreationService,
        ServiceClonerLifeCycleHookServiceInterface $serviceClonerLifeCycleHookService,
        ServiceClonerNamingServiceInterface $serviceClonerNamingService,
        ServiceClonerStateService $serviceClonerStateService,
        ContainerStateServiceInterface $containerStateService,
        SluggerInterface $slugger
    ) {
        $this->dockerConfiguration = $dockerConfiguration;
        $this->configuration = $this->dockerConfiguration->getConfiguration();
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->zfsService = $filesystemService;
        $this->slugger = $slugger;
        $this->containerStateService = $containerCreationService;
        $this->dockerStateService = $containerStateService;
        $this->serviceClonerLifeCycleHookService = $serviceClonerLifeCycleHookService;
        $this->serviceClonerNamingService = $serviceClonerNamingService;
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function start(string $masterName, string $instanceName, int $index): void
    {
        $this->logger->debug(sprintf('-------------%s----------', $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@')));
        $this->startFilesystem($masterName, $instanceName);
        $this->startDocker($masterName, $instanceName, $index);
        $this->serviceClonerStateService->saveState($masterName, $instanceName, $index);
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
}
