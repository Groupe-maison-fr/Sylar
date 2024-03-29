<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface ServiceClonerServiceInterface
{
    public function startMaster(string $masterName): void;

    public function startService(string $masterName, string $instanceName, ?int $index): void;

    public function stop(string $masterName, string $instanceName): void;

    public function restartService(string $masterName, string $instanceName, ?int $index): void;
}
