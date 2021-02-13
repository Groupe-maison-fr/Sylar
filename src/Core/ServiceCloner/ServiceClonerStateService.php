<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function createState(string $masterName, string $instanceName, int $index): void
    {
        $serviceClonerStatusDTO = new ServiceClonerStatusDTO(
            $masterName,
            $instanceName,
            $index,
            $this->serviceClonerNamingService->getDockerName($masterName, $instanceName),
            $this->serviceClonerNamingService->getZfsFilesystemName($masterName, $instanceName),
            $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName),
            time()
        );
        $this->filesystem->dumpFile(
            $this->getStateFileName($masterName, $instanceName),
            json_encode($serviceClonerStatusDTO->toArray(), JSON_PRETTY_PRINT)
        );
    }

    public function loadState(string $masterName, string $instanceName): ?ServiceClonerStatusDTO
    {
        return $this->refreshState(ServiceClonerStatusDTO::createFromArray(
            json_decode(file_get_contents($this->getStateFileName($masterName, $instanceName)), true)
        ));
    }

    public function hasMasterDependantService(string $masterName): bool
    {
        return !(new ArrayCollection($this->getStates()))
            ->filter(fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getMasterName() === $masterName)
            ->filter(fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => !$serviceClonerStatusDTO->isMaster())
            ->isEmpty();
    }

    public function deleteState(string $masterName, string $instanceName): void
    {
        unlink($this->getStateFileName($masterName, $instanceName));
    }

    public function refreshState(ServiceClonerStatusDTO $serviceClonerStatusDTO): ServiceClonerStatusDTO
    {
        $zfsFilesystemPath = sprintf(
            '/%s/%s',
            $this->configurationService->getConfiguration()->getZpoolName(),
            $this->serviceClonerNamingService->getFullName(
                $serviceClonerStatusDTO->getMasterName(),
                $serviceClonerStatusDTO->getInstanceName(),
                '-'
            )
        );

        if ($this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            $serviceClonerStatusDTO->setZfsFilesystem(
                $this->zfsService->getFilesystem($zfsFilesystemPath)
            );
        }

        $serviceClonerStatusDTO->setDockerState(
            $this->dockerStateService->dockerState(
                $this->serviceClonerNamingService->getDockerName(
                    $serviceClonerStatusDTO->getMasterName(),
                    $serviceClonerStatusDTO->getInstanceName()
                )
            )
        );

        return $serviceClonerStatusDTO;
    }

    public function getStates(): array
    {
        $states = array_filter(array_values(array_map(function (SplFileInfo $filename) {
            $rawData = json_decode(file_get_contents($filename->getPathname()), true);

            return $this->refreshState(ServiceClonerStatusDTO::createFromArray($rawData));
        }, iterator_to_array(Finder::create()
            ->files()
            ->name('*.json')
            ->in($this->configurationService->getConfiguration()->getstateRoot())->getIterator()
        ))));

        uasort($states, function (ServiceClonerStatusDTO $stateA, ServiceClonerStatusDTO $stateB) {
            return $stateA->getContainerName() <=> $stateB->getContainerName();
        });

        return $states;
    }

    private function getStateFileName(string $masterName, string $instanceName): string
    {
        return sprintf(
            '%s/%s.json',
            $this->configurationService->getConfiguration()->getstateRoot(),
            $this->serviceClonerNamingService->getFullName(
                $masterName,
                $instanceName,
                '@'
            )
        );
    }
}
