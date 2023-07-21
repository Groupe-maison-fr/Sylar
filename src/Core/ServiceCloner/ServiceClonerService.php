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
use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Docker\API\Exception\ContainerDeleteNotFoundException;
use DomainException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ServiceClonerService implements ServiceClonerServiceInterface
{
    private ServiceCloner $configuration;

    public function __construct(
        private ConfigurationServiceInterface $dockerConfiguration,
        private LoggerInterface $logger,
        private FilesystemServiceInterface $zfsService,
        private ContainerCreationServiceInterface $containerCreationService,
        private ContainerStopServiceInterface $containerStopService,
        private ContainerDeleteServiceInterface $containerDestroyService,
        private ServiceClonerLifeCycleHookServiceInterface $serviceClonerLifeCycleHookService,
        private ServiceClonerNamingServiceInterface $serviceClonerNamingService,
        private ServiceClonerStateService $serviceClonerStateService,
        private ContainerStateServiceInterface $dockerStateService,
        private IndexManagerServiceInterface $indexManagerService,
        private SluggerInterface $slugger,
        private ServerSideEventPublisherInterface $serverSideEventPublisher,
    ) {
        $this->configuration = $this->dockerConfiguration->getConfiguration();
    }

    public function startMaster(string $masterName): void
    {
        try {
            $this->assertStartMasterParameters($masterName);
            $this->start($masterName, ServiceClonerNamingServiceInterface::MASTER_NAME, 0);
        } catch (Exception $exception) {
            $this->publishError($exception->getMessage());
            throw $exception;
        }
    }

    public function startService(string $masterName, string $instanceName, ?int $index): void
    {
        try {
            $this->assertStartServiceParameters($masterName, $instanceName, $index);
            $this->start($masterName, $instanceName, $index);
        } catch (Exception $exception) {
            $this->publishError($exception->getMessage());
            throw $exception;
        }
    }

    public function restartService(string $masterName, string $instanceName, ?int $index): void
    {
        $this->assertStartServiceParameters($masterName, $instanceName, $index);
        try {
            $this->stop($masterName, $instanceName);
            sleep(5);
        } catch (NonExistingServiceInstanceException|NonExistingServiceStateFileException|ContainerDeleteNotFoundException $exception) {
        }
        $this->start($masterName, $instanceName, $index);
    }

    private function start(string $masterName, string $instanceName, ?int $index): void
    {
        if ($index === null) {
            $index = $this->indexManagerService->getNextAvailable($masterName);
        }
        $this->logger->debug(sprintf('-------------%s----------', $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@')));
        $this->startFilesystem($masterName, $instanceName);
        $this->startDocker($masterName, $instanceName, $index);
        $this->serverSideEventPublisher->publish('sylar', [
            'type' => 'serviceCloner',
            'action' => 'start',
            'data' => [
                'masterName' => $masterName,
                'instanceName' => $instanceName,
                'index' => $index,
            ],
        ]);
    }

    public function stop(string $masterName, string $instanceName): void
    {
        try {
            $this->assertStopParameters($masterName, $instanceName);

            $serviceState = $this->serviceClonerStateService->loadState($masterName, $instanceName);
            if ($serviceState === null) {
                throw new NonExistingServiceInstanceException($masterName, $instanceName);
            }

            if ($this->serviceClonerNamingService->isMasterName($instanceName) && $this->serviceClonerStateService->hasMasterDependantService($masterName)) {
                $dependantServiceNames = array_unique(array_map(
                    fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getInstanceName(),
                    $this->serviceClonerStateService->getMasterDependantService($masterName)->toArray(),
                ));
                asort($dependantServiceNames);
                throw new DomainException(sprintf('Can not delete "%s", some dependant services are still there [%s]', $masterName, implode(',', $dependantServiceNames)));
            }

            $containerParameter = new ContainerParameterDTO(
                $serviceState->getContainerName(),
                $serviceState->getIndex(),
                $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName),
            );

            $service = $this->configuration->getServiceByName($masterName);
            if ($service === null) {
                throw new NonExistingServiceException($masterName);
            }
            $this->containerStopService->stop($serviceState->getContainerName());
            $this->containerDestroyService->delete($serviceState->getContainerName());

            $this->serviceClonerLifeCycleHookService->postDestroy($service, $containerParameter);
            $this->stopFilesystem($masterName, $instanceName);
            $this->serverSideEventPublisher->publish('sylar', [
                'type' => 'serviceCloner',
                'action' => 'stop',
                'data' => [
                    'masterName' => $masterName,
                    'instanceName' => $instanceName,
                ],
            ]);
        } catch (Exception $exception) {
            $this->publishError($exception->getMessage());
            throw $exception;
        }
    }

    private function startDocker(string $masterName, string $instanceName, int $index): void
    {
        $containerName = $this->serviceClonerNamingService->getDockerName($masterName, $instanceName);

        $containerParameter = new ContainerParameterDTO(
            $containerName,
            $index,
            $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName),
        );

        if ($this->dockerStateService->dockerState($containerName) === 'running') {
            return;
        }

        $service = $this->configuration->getServiceByName($masterName);
        if ($service === null) {
            throw new NonExistingServiceException($masterName);
        }

        $this->serviceClonerLifeCycleHookService->preStart($service, $containerParameter);
        $this->containerCreationService->createDocker(
            $containerParameter,
            $service,
            [
                'launcher' => 'sylar',
            ] + $this->serviceClonerStateService->createServiceClonerStatusDTO($masterName, $instanceName, $index)->toArray(),
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

        if ($instanceName === ServiceClonerNamingServiceInterface::MASTER_NAME) {
            $this->zfsService->createFilesystem($zfsFilesystemPath);

            return;
        }

        if ($this->zfsService->isSnapshoted($zfsFilesystemPath)) {
            return;
        }

        $filesystem = sprintf(
            '%s/%s',
            $this->dockerConfiguration->getConfiguration()->zpoolName,
            $this->slugger->slug($masterName)->toString(),
        );
        $snapshotName = $this->slugger->slug($instanceName)->toString();

        $this->zfsService->createSnapshot($filesystem, $snapshotName);
        $this->zfsService->cloneSnapshot($filesystem, $snapshotName);
    }

    private function stopFilesystem(string $masterName, string $instanceName): void
    {
        $zfsFilesystemName = $this->dockerConfiguration->getConfiguration()->zpoolName . '/' . $masterName;
        /*
        if (!$this->zfsService->hasSnapshot($zfsFilesystemName, $instanceName)) {
            $this->logger->debug(sprintf('Can not stop filesystem, !hasSnapshot %s', $instanceName));

            return;
        }
        */
        $zfsFilesystemPath = $this->serviceClonerNamingService->getZfsFilesystempath($masterName, $instanceName);
        if (!$this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            $this->logger->debug(sprintf('Can not stop filesystem, !hasFilesystem %s', $zfsFilesystemPath));

            return;
        }

        if ($instanceName === ServiceClonerNamingServiceInterface::MASTER_NAME && $this->zfsService->isSnapshoted($zfsFilesystemName)) {
            $this->logger->debug(sprintf('Can not stop filesystem, !isSnapshoted %s', $zfsFilesystemName));

            return;
        }

        if ($instanceName === ServiceClonerNamingServiceInterface::MASTER_NAME) {
            $this->zfsService->destroyFilesystem($zfsFilesystemName);

            $this->logger->debug(sprintf('Stop master filesystem %s', $zfsFilesystemName));

            return;
        }

        $filesystem = sprintf(
            '%s/%s',
            $this->dockerConfiguration->getConfiguration()->zpoolName,
            $this->slugger->slug($masterName)->toString(),
        );

        $this->zfsService->destroySnapshot($filesystem, $this->slugger->slug($instanceName)->toString(), true);
    }

    private function assertStartMasterParameters(string $masterName): void
    {
        if ($this->dockerConfiguration->getConfiguration()->getServiceByName($masterName) === null) {
            throw new StartServiceException(sprintf('Service name %s does not exists', $masterName));
        }
    }

    private function assertStartServiceParameters(string $masterName, string $instanceName, ?int $index): void
    {
        if ($instanceName === ServiceClonerNamingServiceInterface::MASTER_NAME) {
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

    private function publishError(string $errorMessage): void
    {
        $this->serverSideEventPublisher->publish('sylar', [
            'type' => 'serviceCloner',
            'action' => 'error',
            'data' => [
                'message' => $errorMessage,
            ],
        ]);
    }
}
