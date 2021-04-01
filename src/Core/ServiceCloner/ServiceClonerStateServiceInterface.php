<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface ServiceClonerStateServiceInterface
{
    public function createState(string $masterName, string $instanceName, int $index): void;

    public function refreshState(ServiceClonerStatusDTO $serviceClonerStatusDTO): ServiceClonerStatusDTO;

    public function loadState(string $masterName, string $instanceName): ?ServiceClonerStatusDTO;

    public function hasMasterDependantService(string $masterName): bool;

    public function deleteState(string $masterName, string $instanceName): void;

    /** @return ServiceClonerStatusDTO[] */
    public function getStates(): array;
}
