<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface ServiceClonerStateServiceInterface
{
    public function saveState(string $masterName, string $instanceName, int $index): void;

    public function getState(string $masterName, string $instanceName): ServiceClonerStatusDTO;

    public function getStates(): array;
}
