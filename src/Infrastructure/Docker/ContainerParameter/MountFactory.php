<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Mount;
use Docker\API\Model\Mount as DockerApiModelMount;
use Symfony\Component\Filesystem\Filesystem;

final class MountFactory implements MountFactoryInterface
{
    private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator;
    private Filesystem $filesystem;

    public function __construct(
        ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
        Filesystem $filesystem,
    ) {
        $this->configurationExpressionGenerator = $configurationExpressionGenerator;
        $this->filesystem = $filesystem;
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
