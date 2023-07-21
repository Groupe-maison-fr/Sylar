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

/**
 * @phpstan-type dumpStruct array{
 *       preStartCommands: array{
 *           name: ?string,
 *           value: ?string
 *       }|string,
 *       postStartWaiters: array{
 *           name: ?string,
 *           value: ?string
 *       }|string,
 *       postStartCommands: array{
 *           name: ?string,
 *           value: ?string
 *       }|string,
 *       postDestroyCommands: array{
 *           name: ?string,
 *           value: ?string
 *       }|string
 *   }|array{
 *       source: ?string,
 *       target: ?string
 *   }|array{
 *       containerPort: ?string,
 *       hostPort: ?string,
 *       hostIp: ?string
 *   }|array{
 *       executionEnvironment: ?string,
 *       command: array{
 *           name: ?string,
 *           value: ?string
 *       }|string
 *   }|array{
 *       executionEnvironment: ?string,
 *       command: array{
 *           name: ?string,
 *           value: ?string
 *       }|string
 *   }|array{
 *       type: ?string,
 *       expression: ?string,
 *       timeout: int
 *   }|array{
 *       executionEnvironment: ?string,
 *       command: array{
 *           name: ?string,
 *           value: ?string
 *       }|string
 *   }|array{
 *       name: ?string,
 *       image: ?string,
 *       command: ?string,
 *       entryPoint: ?string,
 *       networkMode: ?string,
 *       lifeCycleHooks: array{
 *           name: ?string,
 *           value: ?string
 *       }|string,
 *       environments: array{
 *           name: ?string,
 *           value: ?string
 *       }|string,
 *       mounts: array{
 *           name: ?string,
 *           value: ?string
 *       }|string
 *   }|array{
 *       configurationRoot: ?string,
 *       stateRoot: ?string,
 *       zpoolName: ?string,
 *       zpoolRoot: ?string,
 *       services: array{
 *           name: ?string,
 *           value: ?string
 *       }
 *   }|array{
 *       name: ?string,
 *       value: ?string
 *   }
 **/
final class ServiceClonerArrayDumperService
{
    public function __construct(
        private ConfigurationServiceInterface $dockerConfiguration,
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    private function evaluate(ContainerParameterDTO $containerParameter, ?string $expression): ?string
    {
        if ($expression === null) {
            return null;
        }

        return $this->configurationExpressionGenerator->generate($containerParameter, $expression);
    }

    /**
     * @phpstan-return dumpStruct|string|bool
     */
    public function dump(ContainerParameterDTO $containerParameter): array|string|bool
    {
        return $this->dumpNode($containerParameter, $this->dockerConfiguration->getConfiguration());
    }

    /**
     * @phpstan-return dumpStruct|string|bool
     */
    private function dumpNode(ContainerParameterDTO $containerParameter, mixed $node): array|string|bool
    {
        switch (true) {
            case is_bool($node):
            case is_string($node):
                return $node;
            case is_array($node):
                return array_map(fn ($item) => $this->dumpNode($containerParameter, $item), $node);
            case $node instanceof Collection:
                return $node->map(fn ($item) => $this->dumpNode($containerParameter, $item))->toArray();
            case $node instanceof Environment:
                return [
                    'name' => $this->evaluate($containerParameter, $node->name),
                    'value' => $this->evaluate($containerParameter, $node->value),
                ];
            case $node instanceof Label:
                return [
                    'name' => $this->evaluate($containerParameter, $node->name),
                    'value' => $this->evaluate($containerParameter, $node->value),
                ];
            case $node instanceof LifeCycleHooks:
                return [
                    'preStartCommands' => $this->dumpNode($containerParameter, $node->postDestroyCommands),
                    'postStartWaiters' => $this->dumpNode($containerParameter, $node->postStartWaiters),
                    'postStartCommands' => $this->dumpNode($containerParameter, $node->postStartCommands),
                    'postDestroyCommands' => $this->dumpNode($containerParameter, $node->postDestroyCommands),
                ];
            case $node instanceof Mount:
                return [
                    'source' => $this->evaluate($containerParameter, $node->source),
                    'target' => $this->evaluate($containerParameter, $node->target),
                ];
            case $node instanceof Port:
                return [
                    'containerPort' => $this->evaluate($containerParameter, $node->containerPort),
                    'hostPort' => $this->evaluate($containerParameter, $node->hostPort),
                    'hostIp' => $this->evaluate($containerParameter, $node->hostIp),
                ];
            case $node instanceof PostDestroyCommand:
                return [
                    'executionEnvironment' => $this->evaluate($containerParameter, $node->executionEnvironment),
                    'command' => $this->dumpNode($containerParameter, $node->command),
                ];
            case $node instanceof PostStartCommand:
                return [
                    'executionEnvironment' => $this->evaluate($containerParameter, $node->executionEnvironment),
                    'command' => $this->dumpNode($containerParameter, $node->command),
                ];
            case $node instanceof PostStartWaiter:
                return [
                    'type' => $this->evaluate($containerParameter, $node->type),
                    'expression' => $this->evaluate($containerParameter, $node->expression),
                    'timeout' => (int) $this->evaluate($containerParameter, (string) $node->timeout),
                ];
            case $node instanceof PreStartCommand:
                return [
                    'executionEnvironment' => $this->evaluate($containerParameter, $node->executionEnvironment),
                    'command' => $this->dumpNode($containerParameter, $node->command),
                ];
            case $node instanceof Service:
                return [
                    'name' => $this->evaluate($containerParameter, $node->name),
                    'image' => $this->evaluate($containerParameter, $node->image),
                    'command' => $this->evaluate($containerParameter, $node->command),
                    'entryPoint' => $this->evaluate($containerParameter, $node->entryPoint),
                    'networkMode' => $this->evaluate($containerParameter, $node->networkMode),
                    'lifeCycleHooks' => $this->dumpNode($containerParameter, $node->lifeCycleHooks),
                    'environments' => $this->dumpNode($containerParameter, $node->environments),
                    'mounts' => $this->dumpNode($containerParameter, $node->mounts),
                    'ports' => $this->dumpNode($containerParameter, $node->ports),
                    'labels' => $this->dumpNode($containerParameter, $node->labels),
                ];
            case $node instanceof ServiceCloner:
                return [
                    'configurationRoot' => $this->evaluate($containerParameter, $node->configurationRoot),
                    'stateRoot' => $this->evaluate($containerParameter, $node->stateRoot),
                    'zpoolName' => $this->evaluate($containerParameter, $node->zpoolName),
                    'zpoolRoot' => $this->evaluate($containerParameter, $node->zpoolRoot),
                    'services' => $this->dumpNode($containerParameter, $node->services),
                ];
        }

        throw new DomainException(sprintf('Unknown type to map %s', json_encode($node)));
    }
}
