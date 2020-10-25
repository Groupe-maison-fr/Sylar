<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Port;
use Docker\API\Model\PortBinding;

interface PortBindingFactoryInterface
{
    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Port $port): PortBinding;
}
