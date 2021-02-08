<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface ServiceClonerStateServiceInterface
{
    public function deleteState(string $masterName, string $instanceName): void;

    public function saveState(string $masterName, string $instanceName, int $index): void;

    public function getState(string $masterName, string $instanceName): ?ServiceClonerStatusDTO;

    /** @return ServiceClonerStatusDTO[] */
    public function getStates(): array;
}
