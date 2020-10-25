<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Mount;
use Docker\API\Model\Mount as DockerApiModelMount;

interface MountFactoryInterface
{
    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Mount $port): DockerApiModelMount;
}
