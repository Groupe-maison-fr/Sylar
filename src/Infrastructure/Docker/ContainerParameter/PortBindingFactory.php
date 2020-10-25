<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Port;
use Docker\API\Model\PortBinding;

final class PortBindingFactory implements PortBindingFactoryInterface
{
    /**
     * @var ConfigurationExpressionGenerator
     */
    private $configurationExpressionGenerator;

    public function __construct(
        ConfigurationExpressionGenerator $configurationExpressionGenerator
    ) {
        $this->configurationExpressionGenerator = $configurationExpressionGenerator;
    }

    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Port $port): PortBinding
    {
        $portBinding = new PortBinding();
        if ($port->getHostIp() !== null) {
            $portBinding->setHostIp(
                $this->configurationExpressionGenerator->generate($containerParameter, $port->getHostIp())
            );
        }
        if ($port->getHostPort() !== null) {
            $portBinding->setHostPort(
                $this->configurationExpressionGenerator->generate($containerParameter, $port->getHostPort())
            );
        }

        return $portBinding;
    }
}
