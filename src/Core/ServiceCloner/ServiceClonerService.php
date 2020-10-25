<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use App\Core\ServiceCloner\Exception\NonExistingServiceException;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Zfs\ZfsServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ServiceClonerService implements ServiceClonerServiceInterface
{
    private const MASTER_NAME = 'master';
    /**
     * @var ConfigurationServiceInterface
     */
    private $dockerConfiguration;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ZfsServiceInterface
     */
    private $zfsService;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var ContainerCreationServiceInterface
     */
    private $dockerService;

    /**
     * @var ServiceCloner
     */
    private $configuration;

    /**
     * @var ContainerStateServiceInterface
     */
    private $dockerStateService;

    /**
     * @var ServiceClonerLifeCycleHookServiceInterface
     */
    private $serviceClonerLifeCycleHookService;

    /**
     * @var ServiceClonerNamingServiceInterface
     */
    private $serviceClonerNamingService;

    /**
     * @var ServiceClonerStateService
     */
    private $serviceClonerStateService;

    public function __construct(
        ConfigurationServiceInterface $dockerConfiguration,
        LoggerInterface $logger,
        Filesystem $filesystem,
        ZfsServiceInterface $zfs,
        ContainerCreationServiceInterface $dockerService,
        ServiceClonerLifeCycleHookServiceInterface $serviceClonerLifeCycleHookService,
        ServiceClonerNamingServiceInterface $serviceClonerNamingService,
        ServiceClonerStateService $serviceClonerStateService,
        ContainerStateServiceInterface $dockerStateService,
        SluggerInterface $slugger
    ) {
        $this->dockerConfiguration = $dockerConfiguration;
        $this->configuration = $this->dockerConfiguration->getConfiguration();
        $this->logger = $logger;
        $this->filesystem = $filesystem;
        $this->zfsService = $zfs;
        $this->slugger = $slugger;
        $this->dockerService = $dockerService;
        $this->dockerStateService = $dockerStateService;
        $this->serviceClonerLifeCycleHookService = $serviceClonerLifeCycleHookService;
        $this->serviceClonerNamingService = $serviceClonerNamingService;
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function start(string $masterName, string $instanceName, int $index): void
    {
        dump(sprintf('-------------%s----------', $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@')));
        $this->startZfs($masterName, $instanceName);
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
        $this->dockerService->createDocker(
            $containerParameter,
            $service
        );
        $this->serviceClonerLifeCycleHookService->postStartWaiter($service, $containerParameter);
        $this->serviceClonerLifeCycleHookService->postStart($service, $containerParameter);
    }

    private function startZfs(string $masterName, string $instanceName): void
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
