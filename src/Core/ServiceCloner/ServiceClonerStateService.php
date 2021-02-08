<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use Psr\Log\LoggerInterface;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class ServiceClonerStateService implements ServiceClonerStateServiceInterface
{
    private FilesystemServiceInterface $zfsService;

    private Filesystem $filesystem;

    private ServiceClonerNamingServiceInterface $serviceClonerNamingService;

    private ConfigurationServiceInterface $configurationService;

    private LoggerInterface $logger;

    private ContainerStateServiceInterface $dockerStateService;

    public function __construct(
        FilesystemServiceInterface $zfs,
        Filesystem $filesystem,
        LoggerInterface $logger,
        ContainerStateServiceInterface $dockerStateService,
        ConfigurationServiceInterface $configurationService,
        ServiceClonerNamingServiceInterface $serviceClonerNamingService
    ) {
        $this->zfsService = $zfs;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->serviceClonerNamingService = $serviceClonerNamingService;
        $this->configurationService = $configurationService;
        $this->dockerStateService = $dockerStateService;
    }

    public function saveState(string $masterName, string $instanceName, int $index): void
    {
        $this->filesystem->dumpFile(sprintf(
            '%s/%s.json',
            $this->configurationService->getConfiguration()->getstateRoot(),
            $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@')
        ), json_encode([
            'containerName' => $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '_'),
            'masterName' => $masterName,
            'instanceName' => $instanceName,
            'instanceIndex' => $index,
            'zfsFilesystemName' => $this->serviceClonerNamingService->getZfsFilesystemName($masterName, $instanceName),
            'zfsFilesystem' => $this->zfsService->getFilesystem($this->serviceClonerNamingService->getZfsFilesystemName($masterName, $instanceName)),
            'time' => time(),
        ]));
    }

    public function deleteState(string $masterName, string $instanceName): void
    {
        $fullName = $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@');
        $stateFilename = sprintf('%s/%s.json', $this->configurationService->getConfiguration()->getstateRoot(), $fullName);
        unlink($stateFilename);
    }

    public function getState(string $masterName, string $instanceName): ?ServiceClonerStatusDTO
    {
        $fullName = $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@');
        $stateFilename = sprintf('%s/%s.json', $this->configurationService->getConfiguration()->getstateRoot(), $fullName);

        $serviceClonerStatusDTO = new ServiceClonerStatusDTO($masterName, $instanceName);

        if (!$this->filesystem->exists($stateFilename)) {
            $this->logger->info(sprintf('State file "%s" does not exists', $stateFilename));

            return null;
        }
        $rawState = json_decode(file_get_contents($stateFilename), true);
        $serviceClonerStatusDTO->setStateFilename($stateFilename);
        $serviceClonerStatusDTO->setContainerName($rawState['containerName']);

        $zfsFilesystemPath = sprintf(
            '/%s/%s',
            $this->configurationService->getConfiguration()->getZpoolName(),
            $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '-')
        );
        if (!$this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            $this->logger->info(sprintf('ZFS "%s" does not exists', $zfsFilesystemPath));

            return null;
        }
        $serviceClonerStatusDTO->setZfsPath($zfsFilesystemPath);

        if ($this->dockerStateService->dockerState($fullName) !== 'running') {
            $this->logger->info(sprintf('Docker "%s" does not exists', $fullName));

            return null;
        }

        if ($this->serviceClonerNamingService->isMasterName($instanceName)) {
            $serviceClonerStatusDTO->setIsMaster(false);

            return $serviceClonerStatusDTO;
        }
        $serviceClonerStatusDTO->setIsMaster(true);

        $serviceClonerStatusDTO->setIndex($rawState['instanceIndex']);

        return $serviceClonerStatusDTO;
    }

    public function getStates(): array
    {
        $states = array_values(array_map(function (SplFileInfo $filename) {
            $rawData = json_decode(file_get_contents($filename->getPathname()), true);
            return $this->getState($rawData['masterName'], $rawData['instanceName']);
        }, iterator_to_array(Finder::create()
            ->files()
            ->name('*.json')
            ->in($this->configurationService->getConfiguration()->getstateRoot())->getIterator()
        )));

        uasort($states, function (ServiceClonerStatusDTO $stateA, ServiceClonerStatusDTO $stateB) {
            return $stateA->getContainerName() <=> $stateB->getContainerName();
        });

        return $states;
    }
}
