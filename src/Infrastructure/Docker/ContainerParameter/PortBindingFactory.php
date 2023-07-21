<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Port;
use Docker\API\Model\PortBinding;

final readonly class PortBindingFactory implements PortBindingFactoryInterface
{
    public function __construct(
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Port $port): PortBinding
    {
        $portBinding = new PortBinding();
        if ($port->hostIp !== null) {
            $portBinding->setHostIp(
                $this->configurationExpressionGenerator->generate($containerParameter, $port->hostIp),
            );
        }
        if ($port->hostPort !== null) {
            $portBinding->setHostPort(
                $this->configurationExpressionGenerator->generate($containerParameter, $port->hostPort),
            );
        }

        return $portBinding;
    }
}
