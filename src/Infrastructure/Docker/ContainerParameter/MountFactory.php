<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Mount;
use Docker\API\Model\Mount as DockerApiModelMount;

final class MountFactory implements MountFactoryInterface
{
    public function __construct(
        private readonly ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Mount $mount): DockerApiModelMount
    {
        $dockerMount = new DockerApiModelMount();
        $dockerMount->setSource(
            $this->configurationExpressionGenerator->generate($containerParameter, $mount->source),
        );
        $dockerMount->setTarget(
            $this->configurationExpressionGenerator->generate($containerParameter, $mount->target),
        );
        $dockerMount->setType('bind');

        return $dockerMount;
    }
}
