<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;

interface ServiceClonerLifeCycleHookServiceInterface
{
    public function preStart(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void;

    public function postStartWaiter(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void;

    public function postStart(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void;

    public function postDestroy(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void;
}
