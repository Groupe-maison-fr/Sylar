<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

interface StringParameterFactoryInterface
{
    public function createFromConfiguration(ContainerParameterDTO $containerParameter, ?string $string): ?string;
}
