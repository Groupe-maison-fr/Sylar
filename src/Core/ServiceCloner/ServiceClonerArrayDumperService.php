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

final class ServiceClonerArrayDumperService
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

    private function evaluate(ContainerParameterDTO $containerParameter, ?string $expression): ?string
    {
        if ($expression === null) {
            return null;
        }

        return $this->configurationExpressionGenerator->generate($containerParameter, $expression);
    }

    public function dump(ContainerParameterDTO $containerParameter): array
    {
        return $this->dumpNode($containerParameter, $this->dockerConfiguration->getConfiguration());
    }

    /**
     * @return string|array
     */
    private function dumpNode(ContainerParameterDTO $containerParameter, $node)
    {
        switch (true) {
            case is_array($node):
            case is_string($node):
                return $node;
            case $node instanceof Collection:
                return $node->map(fn ($item) => $this->dumpNode($containerParameter, $item))->toArray();
            case $node instanceof Environment:
                return [
                    'name' => $this->evaluate($containerParameter, $node->getName()),
                    'value' => $this->evaluate($containerParameter, $node->getValue()),
                ];
            case $node instanceof Label:
                return [
                    'name' => $this->evaluate($containerParameter, $node->getName()),
                    'value' => $this->evaluate($containerParameter, $node->getValue()),
                ];
            case $node instanceof LifeCycleHooks:
                return [
                    'preStartCommands' => $this->dumpNode($containerParameter, $node->getPostDestroyCommands()),
                    'postStartWaiters' => $this->dumpNode($containerParameter, $node->getPostStartWaiters()),
                    'postStartCommands' => $this->dumpNode($containerParameter, $node->getPostStartCommands()),
                    'postDestroyCommands' => $this->dumpNode($containerParameter, $node->getPostDestroyCommands()),
                ];
            case $node instanceof Mount:
                return [
                    'source' => $this->evaluate($containerParameter, $node->getSource()),
                    'target' => $this->evaluate($containerParameter, $node->getTarget()),
                ];
            case $node instanceof Port:
                return [
                    'containerPort' => $this->evaluate($containerParameter, $node->getContainerPort()),
                    'hostPort' => $this->evaluate($containerParameter, $node->getHostPort()),
                    'hostIp' => $this->evaluate($containerParameter, $node->getHostIp()),
                ];
            case $node instanceof PostDestroyCommand:
                return [
                    'executionEnvironment' => $this->evaluate($containerParameter, $node->getExecutionEnvironment()),
                    'command' => $this->dumpNode($containerParameter, $node->getCommand()),
                ];
            case $node instanceof PostStartCommand:
                return [
                    'executionEnvironment' => $this->evaluate($containerParameter, $node->getExecutionEnvironment()),
                    'command' => $this->dumpNode($containerParameter, $node->getCommand()),
                ];
            case $node instanceof PostStartWaiter:
                return [
                    'type' => $this->evaluate($containerParameter, $node->getType()),
                    'expression' => $this->evaluate($containerParameter, $node->getExpression()),
                    'timeout' => (int) $this->evaluate($containerParameter, (string) $node->getTimeout()),
                ];
            case $node instanceof PreStartCommand:
                return [
                    'executionEnvironment' => $this->evaluate($containerParameter, $node->getExecutionEnvironment()),
                    'command' => $this->dumpNode($containerParameter, $node->getCommand()),
                ];
            case $node instanceof Service:
                return [
                    'name' => $this->evaluate($containerParameter, $node->getName()),
                    'image' => $this->evaluate($containerParameter, $node->getImage()),
                    'command' => $this->evaluate($containerParameter, $node->getCommand()),
                    'entryPoint' => $this->evaluate($containerParameter, $node->getEntryPoint()),
                    'networkMode' => $this->evaluate($containerParameter, $node->getNetworkMode()),
                    'lifeCycleHooks' => $this->dumpNode($containerParameter, $node->getLifeCycleHooks()),
                    'environments' => $this->dumpNode($containerParameter, $node->getEnvironments()),
                    'mounts' => $this->dumpNode($containerParameter, $node->getMounts()),
                    'ports' => $this->dumpNode($containerParameter, $node->getPorts()),
                    'labels' => $this->dumpNode($containerParameter, $node->getLabels()),
                ];
            case $node instanceof ServiceCloner:
                return [
                    'configurationRoot' => $this->evaluate($containerParameter, $node->getConfigurationRoot()),
                    'stateRoot' => $this->evaluate($containerParameter, $node->getstateRoot()),
                    'zpoolName' => $this->evaluate($containerParameter, $node->getZpoolName()),
                    'zpoolRoot' => $this->evaluate($containerParameter, $node->getZpoolRoot()),
                    'services' => $this->dumpNode($containerParameter, $node->getServices()),
                ];
        }

        throw new DomainException('Unknown type to map ' . json_encode($node));
    }
}
