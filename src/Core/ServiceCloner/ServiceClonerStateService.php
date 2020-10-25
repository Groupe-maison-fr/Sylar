<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Zfs\ZfsServiceInterface;
use Psr\Log\LoggerInterface;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class ServiceClonerStateService implements ServiceClonerStateServiceInterface
{
    /**
     * @var ZfsServiceInterface
     */
    private $zfsService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ServiceClonerNamingServiceInterface
     */
    private $serviceClonerNamingService;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ContainerStateServiceInterface
     */
    private $dockerStateService;

    public function __construct(
        ZfsServiceInterface $zfs,
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

    public function getState(string $masterName, string $instanceName): ServiceClonerStatusDTO
    {
        $fullName = $this->serviceClonerNamingService->getFullName($masterName, $instanceName, '@');
        $stateFilename = sprintf('%s/%s.json', $this->configurationService->getConfiguration()->getstateRoot(), $fullName);

        $serviceClonerStatusDTO = new ServiceClonerStatusDTO($masterName, $instanceName);

        if (!$this->filesystem->exists($stateFilename)) {
            $this->logger->info(sprintf('State file "%s" does not exists', $stateFilename));

            return $serviceClonerStatusDTO;
        }
        $serviceClonerStatusDTO->setStateFilename($stateFilename);

        $zfsFilesystemPath = sprintf('%s/%s', $this->configurationService->getConfiguration()->getZpoolName(), $fullName);
        if (!$this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            $this->logger->info(sprintf('ZFS "%s" does not exists', $zfsFilesystemPath));

            return $serviceClonerStatusDTO;
        }
        $serviceClonerStatusDTO->setZfsPath($zfsFilesystemPath);

        if ($this->dockerStateService->dockerState($fullName) !== 'running') {
            $this->logger->info(sprintf('Docker "%s" does not exists', $fullName));

            return $serviceClonerStatusDTO;
        }

        if ($this->serviceClonerNamingService->isMasterName($instanceName)) {
            $serviceClonerStatusDTO->setIsMaster(false);

            return $serviceClonerStatusDTO;
        }
        $serviceClonerStatusDTO->setIsMaster(true);

        return $serviceClonerStatusDTO;
    }

    public function getStates(): array
    {
        $states = array_values(array_map(function (SplFileInfo $filename) {
            return json_decode(file_get_contents($filename->getPathname()), true);
        }, iterator_to_array(Finder::create()
            ->files()
            ->name('*.json')
            ->in($this->configurationService->getConfiguration()->getstateRoot())->getIterator()
        )));

        uasort($states, function (array $stateA, array $stateB) {
            return $stateA['containerName'] <=> $stateB['containerName'];
        });

        return $states;
    }
}
