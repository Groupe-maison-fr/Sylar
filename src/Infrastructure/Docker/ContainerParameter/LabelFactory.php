<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Label;

final readonly class LabelFactory implements LabelFactoryInterface
{
    public function __construct(
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Label $label): array
    {
        return [
            $this->configurationExpressionGenerator->generate($containerParameter, $label->name),
            $this->configurationExpressionGenerator->generate($containerParameter, $label->value),
        ];
    }
}
