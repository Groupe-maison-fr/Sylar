<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface ServiceClonerServiceInterface
{
    public function start(string $masterName, string $instanceName, ?int $index): void;

    public function stop(string $masterName, string $instanceName): void;
}
