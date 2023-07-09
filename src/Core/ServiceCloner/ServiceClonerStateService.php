<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Exception\NonExistingServiceStateFileException;
use App\Infrastructure\Docker\ContainerFinderServiceInterface;
use App\Infrastructure\Docker\ContainerLabelServiceInterface;
use App\Infrastructure\Docker\ContainerStateServiceInterface;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;

final class ServiceClonerStateService implements ServiceClonerStateServiceInterface
{
    public function __construct(
        private FilesystemServiceInterface $zfsService,
        private LoggerInterface $logger,
        private ContainerStateServiceInterface $dockerStateService,
        private ConfigurationServiceInterface $configurationService,
        private ServiceClonerNamingServiceInterface $serviceClonerNamingService,
        private ContainerFinderServiceInterface $containerFinderService,
        private ContainerLabelServiceInterface $containerLabelService,
    ) {
    }

    public function loadState(string $masterName, string $instanceName): ?ServiceClonerStatusDTO
    {
        $dockerName = $this->serviceClonerNamingService->getDockerName($masterName, $instanceName);
        $rawData = $this->containerLabelService->getDockerLabelsByName($dockerName);
        if (empty($rawData)) {
            throw new NonExistingServiceStateFileException($masterName, $instanceName);
        }

        return $this->refreshState(ServiceClonerStatusDTO::createFromArray($rawData));
    }

    public function hasMasterDependantService(string $masterName): bool
    {
        return !$this->getMasterDependantService($masterName)->isEmpty();
    }

    public function refreshState(ServiceClonerStatusDTO $serviceClonerStatusDTO): ServiceClonerStatusDTO
    {
        $zfsFilesystemPath = sprintf(
            '/%s/%s',
            $this->configurationService->getConfiguration()->getZpoolName(),
            $this->serviceClonerNamingService->getFullName(
                $serviceClonerStatusDTO->getMasterName(),
                $serviceClonerStatusDTO->getInstanceName(),
                '-',
            ),
        );

        if ($this->zfsService->hasFilesystem($zfsFilesystemPath)) {
            $serviceClonerStatusDTO->setZfsFilesystem(
                $this->zfsService->getFilesystem($zfsFilesystemPath),
            );
        }

        $dockerName = $this->serviceClonerNamingService->getDockerName(
            $serviceClonerStatusDTO->getMasterName(),
            $serviceClonerStatusDTO->getInstanceName(),
        );

        $serviceClonerStatusDTO->setDockerState(
            $this->dockerStateService->dockerState($dockerName),
        );

        $serviceClonerStatusDTO->setExposedPorts(
            $this->dockerStateService->dockerExposedPorts($dockerName),
        );

        return $serviceClonerStatusDTO;
    }

    public function getStates(): array
    {
        $states = array_filter(
            array_values(
                array_map(
                    fn (string $dockerName) => $this->refreshState(ServiceClonerStatusDTO::createFromArray(
                        $this->containerLabelService->getDockerLabelsByName($dockerName),
                    )),
                    $this->containerFinderService->getDockersByLabel('launcher', 'sylar'),
                ),
            ),
        );
        usort($states, function (ServiceClonerStatusDTO $stateA, ServiceClonerStatusDTO $stateB) {
            if ($stateA->getContainerName() === $stateB->getContainerName()) {
                return $stateA->isMaster() <=> $stateB->isMaster();
            }

            return $stateA->getContainerName() <=> $stateB->getContainerName();
        });

        return $states;
    }

    public function createServiceClonerStatusDTO(string $masterName, string $instanceName, int $index): ServiceClonerStatusDTO
    {
        return new ServiceClonerStatusDTO(
            $masterName,
            $instanceName,
            $index,
            $this->serviceClonerNamingService->getDockerName($masterName, $instanceName),
            $this->serviceClonerNamingService->getZfsFilesystemName($masterName, $instanceName),
            $this->serviceClonerNamingService->getZfsFilesystemPath($masterName, $instanceName),
            time(),
        );
    }

    public function getMasterDependantService(string $masterName): ArrayCollection
    {
        return (new ArrayCollection($this->getStates()))
            ->filter(fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getMasterName() === $masterName)
            ->filter(fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => !$serviceClonerStatusDTO->isMaster());
    }
}
