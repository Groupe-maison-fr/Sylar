<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use Doctrine\Common\Collections\ArrayCollection;

interface ServiceClonerStateServiceInterface
{
    public function refreshState(ServiceClonerStatusDTO $serviceClonerStatusDTO): ServiceClonerStatusDTO;

    public function loadState(string $masterName, string $instanceName): ?ServiceClonerStatusDTO;

    public function hasMasterDependantService(string $masterName): bool;

    /** @return ServiceClonerStatusDTO[] */
    public function getStates(): array;

    /** @return ServiceClonerStatusDTO[] */
    public function getStatesByService(string $serviceName): array;

    public function createServiceClonerStatusDTO(string $masterName, string $instanceName, int $index): ServiceClonerStatusDTO;

    /**
     * @return ArrayCollection<int, ServiceClonerStatusDTO>
     */
    public function getMasterDependantService(string $masterName): ArrayCollection;
}
