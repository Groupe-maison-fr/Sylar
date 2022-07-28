<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Environment;

final class EnvironmentFactory implements EnvironmentFactoryInterface
{
    private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator;

    public function __construct(
        ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator
    ) {
        $this->configurationExpressionGenerator = $configurationExpressionGenerator;
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Environment $environment): string
    {
        return sprintf('%s=%s',
            $this->configurationExpressionGenerator->generate($containerParameter, $environment->getName()),
            $this->configurationExpressionGenerator->generate($containerParameter, $environment->getValue())
        );
    }
}
