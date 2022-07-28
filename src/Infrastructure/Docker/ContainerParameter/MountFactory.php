<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Mount;
use Docker\API\Model\Mount as DockerApiModelMount;

final class MountFactory implements MountFactoryInterface
{
    private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator;

    public function __construct(
        ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator
    ) {
        $this->configurationExpressionGenerator = $configurationExpressionGenerator;
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Mount $mount): DockerApiModelMount
    {
        $dockerMount = new DockerApiModelMount();
        $dockerMount->setSource(
            $this->configurationExpressionGenerator->generate($containerParameter, $mount->getSource())
        );
        $dockerMount->setTarget(
            $this->configurationExpressionGenerator->generate($containerParameter, $mount->getTarget())
        );
        $dockerMount->setType('bind');

        return $dockerMount;
    }
}
