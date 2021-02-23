<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Environment;
use App\Core\ServiceCloner\Configuration\Object\Label;
use App\Core\ServiceCloner\Configuration\Object\LifeCycleHooks;
use App\Core\ServiceCloner\Configuration\Object\Mount;
use App\Core\ServiceCloner\Configuration\Object\Port;
use App\Core\ServiceCloner\Configuration\Object\PostDestroyCommand;
use App\Core\ServiceCloner\Configuration\Object\PostStartCommand;
use App\Core\ServiceCloner\Configuration\Object\PostStartWaiter;
use App\Core\ServiceCloner\Configuration\Object\PreStartCommand;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use App\Infrastructure\Docker\ContainerParameter\ConfigurationExpressionGeneratorInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Doctrine\Common\Collections\Collection;
use DomainException;

final class ServiceClonerCommandLineDumperService
{
    private ConfigurationServiceInterface $dockerConfiguration;

    private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator;

    public function __construct(
        ConfigurationServiceInterface $dockerConfiguration,
        ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator
    ) {
        $this->dockerConfiguration = $dockerConfiguration;
        $this->configurationExpressionGenerator = $configurationExpressionGenerator;
    }

    public function dump(ContainerParameterDTO $container): string
    {
        return $this->dumpNode($container, $this->dockerConfiguration->getConfiguration());
    }

    private function evaluate(ContainerParameterDTO $container, string $expression): ?string
    {
        if ($expression === null) {
            return null;
        }

        return $this->configurationExpressionGenerator->generate($container, $expression);
    }

    private function dumpNode(ContainerParameterDTO $container, $node): ?string
    {
        switch (true) {
            case is_array($node):
                return implode(' ', $node);
            case $node instanceof Collection:
                return implode(' ', $node->map(fn ($item) => $this->dumpNode($container, $item))->toArray());
            case $node instanceof PreStartCommand:
            case $node instanceof PostStartWaiter:
            case $node instanceof PostStartCommand:
            case $node instanceof PostDestroyCommand:
            case $node instanceof LifeCycleHooks:
                return null;
            case $node instanceof Environment:
                return sprintf(
                    '--env %s=%s',
                    $this->evaluate($container, $node->getName()),
                    $this->evaluate($container, $this->evaluate($container, $node->getValue()))
                );
            case $node instanceof Label:
                return sprintf(
                    '--label %s=%s',
                    $this->evaluate($container, $node->getName()),
                    $this->evaluate($container, $node->getValue())
                );
            case $node instanceof Mount:
                return sprintf(
                    '--mount type=bind,target=%s,source=%s',
                    $this->evaluate($container, $node->getTarget()),
                    $this->evaluate($container, $node->getSource())
                );
            case $node instanceof Port:
                return sprintf(
                    '--publish %s:%s:%s',
                    $this->evaluate($container, $node->getHostIp()),
                    str_replace(['/tcp', '/udp'], '', $this->evaluate($container, $node->getHostPort())),
                    $this->evaluate($container, $node->getContainerPort()),
                );
            case $node instanceof Service:
                return implode(' ', array_filter([
                    'docker run',
                    $this->dumpNode($container, $node->getEnvironments()),
                    $this->dumpNode($container, $node->getMounts()),
                    $this->dumpNode($container, $node->getPorts()),
                    $this->dumpNode($container, $node->getLabels()),
                    '--name ' . $this->evaluate($container, $container->getName()),
                    $this->evaluate($container, $node->getImage()),
                    $node->getEntryPoint() != '' ? sprintf('--entrypoint %s', $this->evaluate($container, $node->getEntryPoint())) : '',
                    $this->evaluate($container, $node->getCommand()),
                ]));
            case $node instanceof ServiceCloner:
                return $this->dumpNode($container, $node->getServices());
        }
        throw new DomainException('Unknown type to map');
    }
}
