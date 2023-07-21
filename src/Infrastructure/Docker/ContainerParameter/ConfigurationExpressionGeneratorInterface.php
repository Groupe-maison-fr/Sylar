<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

interface ConfigurationExpressionGeneratorInterface
{
    public function generate(ContainerParameterDTO $containerParameter, string $configurationExpression): string;

    public function evaluate(ContainerParameterDTO $containerParameter, string $configurationExpression): mixed;
}
