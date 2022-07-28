<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;

interface ContainerCreationServiceInterface
{
    public function createDocker(ContainerParameterDTO $containerParameter, Service $service, array $labels): void;
}
