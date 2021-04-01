<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use App\Core\ServiceCloner\Exception\NonExistingServiceException;
use App\Core\ServiceCloner\Exception\NonExistingServiceInstanceException;
use App\Core\ServiceCloner\Exception\NonExistingServiceStateFileException;
use App\Core\ServiceCloner\Exception\StartServiceException;
use App\Core\ServiceCloner\Exception\StopServiceException;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerDeleteServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Docker\ContainerStopServiceInterface;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use Docker\API\Exception\ContainerDeleteNotFoundException;
use DomainException;
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

    public function startMaster(string $masterName): void
    {
        $this->assertStartMasterParameters($masterName);
        $this->start($masterName, 'master', 0);
    }

    public function startService(string $masterName, string $instanceName, ?int $index): void
    {
        $this->assertStartServiceParameters($masterName, $instanceName, $index);
        $this->start($masterName, $instanceName, $index);
    }

    public function restartService(string $masterName, string $instanceName, ?int $index): void
    {
        $this->assertStartServiceParameters($masterName, $instanceName, $index);
        try {
            $this->stop($masterName, $instanceName);
            sleep(5);
        } catch (NonExistingServiceInstanceException | NonExistingServiceStateFileException | ContainerDeleteNotFoundException $exception) {
        }
        $this->start($masterName, $instanceName, $index);
    }

    private function start(string $masterName, string $instanceName, ?int $index): void
    {
        if ($index === null) {
            $index = $this->indexManagerService->getNextAvailable();
        }
        $this->logger->debug(sprintf('-------------%s----------', $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@')));
        $this->startFilesystem($masterName, $instanceName);
        $this->startDocker($masterName, $instanceName, $index);
        $this->serviceClonerStateService->createState($masterName, $instanceName, $index);
    }

    public function stop(string $masterName, string $instanceName): void
    {
        $this->assertStopParameters($masterName, $instanceName);

        $serviceState = $this->serviceClonerStateService->loadState($masterName, $instanceName);
        if ($serviceState === null) {
            throw new NonExistingServiceInstanceException($masterName, $instanceName);
        }

        if ($this->serviceClonerNamingService->isMasterName($instanceName) && $this->serviceClonerStateService->hasMasterDependantService($masterName)) {
            throw new DomainException(sprintf('Can not delete "%s", some dependant services are still there', $masterName));
        }

        $containerParameter = new ContainerParameterDTO(
            $serviceState->getContainerName(),
            $serviceState->getIndex(),
            $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName)
        );

        $service = $this->configuration->getServiceByName($masterName);
        if ($service === null) {
            throw new NonExistingServiceException($masterName);
        }
        $this->containerStopService->stop($serviceState->getContainerName());
        $this->containerDestroyService->delete($serviceState->getContainerName());

        $this->serviceClonerLifeCycleHookService->postDestroy($service, $containerParameter);
        $this->stopFilesystem($masterName, $instanceName);

        $this->serviceClonerStateService->deleteState($masterName, $instanceName);
    }

    private function startDocker(string $masterName, string $instanceName, int $index): void
    {
        $containerName = $this->serviceClonerNamingService->getDockerName($masterName, $instanceName);

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
            return;
        }
        $zfsFilesystemPath = $this->serviceClonerNamingService->getZfsFilesystempath($masterName, $instanceName);
        if (!$this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            return;
        }

        if (!$this->zfsService->isSnapshoted($zfsFilesystemName)) {
            return;
        }

        $filesystem = sprintf(
            '%s/%s',
            $this->dockerConfiguration->getConfiguration()->getZpoolName(),
            $this->slugger->slug($masterName)->toString()
        );
        $snapshotName = $this->slugger->slug($instanceName)->toString();

        $this->zfsService->destroySnapshot($filesystem, $snapshotName, true);
    }

    private function assertStartMasterParameters(string $masterName): void
    {
        if ($this->dockerConfiguration->getConfiguration()->getServiceByName($masterName) === null) {
            throw new StartServiceException(sprintf('Service name %s does not exists', $masterName));
        }
    }

    private function assertStartServiceParameters(string $masterName, string $instanceName, ?int $index): void
    {
        if ($instanceName === 'master') {
            throw new StartServiceException(sprintf('Service name %s can not be master', $instanceName));
        }

        if ($index !== null && (int) $index === 0) {
            throw new StartServiceException(sprintf('Service index %s can not be 0', $instanceName));
        }

        if ($this->dockerConfiguration->getConfiguration()->getServiceByName($masterName) === null) {
            throw new StartServiceException(sprintf('Service name %s does not exists', $masterName));
        }
    }

    private function assertStopParameters(string $masterName, string $instanceName): void
    {
        if ($this->dockerConfiguration->getConfiguration()->getServiceByName($masterName) === null) {
            throw new StopServiceException(sprintf('Service name %s does not exists', $masterName));
        }
    }
}
