<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Environment;

final class EnvironmentFactory implements EnvironmentFactoryInterface
{
    public function __construct(
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Environment $environment): string
    {
        return sprintf(
            '%s=%s',
            $this->configurationExpressionGenerator->generate($containerParameter, $environment->getName()),
            $this->configurationExpressionGenerator->generate($containerParameter, $environment->getValue()),
        );
    }
}
