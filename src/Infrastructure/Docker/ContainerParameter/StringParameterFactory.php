<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

final class StringParameterFactory implements StringParameterFactoryInterface
{
    public function __construct(
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, ?string $string): ?string
    {
        if ($string == null) {
            return null;
        }

        return $this->configurationExpressionGenerator->generate($containerParameter, $string);
    }
}
