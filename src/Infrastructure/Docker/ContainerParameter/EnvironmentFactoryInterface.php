<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Environment;

interface EnvironmentFactoryInterface
{
    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Environment $environment): string;
}
