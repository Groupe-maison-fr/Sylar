<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class TreeBuilderConfiguration implements ConfigurationInterface
{
    public const POST_START_WAITER_LOG_MATCH = 'logMatch';
    public const EXECUTION_ENVIRONMENT_HOST = 'host';
    public const EXECUTION_ENVIRONMENT_MASTER_CONTAINER = 'masterContainer';
    public const EXECUTION_ENVIRONMENT_CLONE_CONTAINER = 'cloneContainer';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('serviceCloner');

        /* @phpstan-ignore-next-line */
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('stateRoot')
                    ->isRequired()
                ->end()
                ->scalarNode('zpoolName')
                    ->isRequired()
                ->end()
                ->scalarNode('zpoolRoot')
                    ->isRequired()
                ->end()
                ->arrayNode('commands')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                            ->end()
                            ->arrayNode('subCommands')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('services')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                            ->end()
                            ->scalarNode('image')
                                ->isRequired()
                            ->end()
                            ->scalarNode('command')->end()
                            ->scalarNode('entrypoint')->end()
                            ->arrayNode('labels')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')->end()
                                        ->scalarNode('value')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('networkMode')->end()
                            ->arrayNode('lifeCycleHooks')
                                ->children()
                                    ->arrayNode('preStartCommands')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('executionEnvironment')
                                                    ->isRequired()
                                                    ->validate()
                                                        ->ifNotInArray([
                                                            self::EXECUTION_ENVIRONMENT_HOST,
                                                            self::EXECUTION_ENVIRONMENT_MASTER_CONTAINER,
                                                        ])
                                                        ->thenInvalid('Invalid execution environment %s')
                                                    ->end()
                                                ->end()
                                                ->arrayNode('command')
                                                    ->scalarPrototype()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                    ->arrayNode('postStartWaiters')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('type')
                                                    ->isRequired()
                                                    ->validate()
                                                        ->ifNotInArray([
                                                            self::POST_START_WAITER_LOG_MATCH,
                                                        ])
                                                        ->thenInvalid('Invalid execution environment %s')
                                                    ->end()
                                                ->end()
                                                ->scalarNode('expression')->end()
                                                ->scalarNode('timeout')
                                                    ->defaultValue('60')
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                    ->arrayNode('postStartCommands')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('executionEnvironment')
                                                    ->isRequired()
                                                    ->validate()
                                                        ->ifNotInArray([
                                                            self::EXECUTION_ENVIRONMENT_HOST,
                                                            self::EXECUTION_ENVIRONMENT_MASTER_CONTAINER,
                                                            self::EXECUTION_ENVIRONMENT_CLONE_CONTAINER,
                                                        ])
                                                        ->thenInvalid('Invalid execution environment %s')
                                                    ->end()
                                                ->end()
                                                ->arrayNode('command')
                                                    ->scalarPrototype()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                    ->arrayNode('postDestroyCommands')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('executionEnvironment')
                                                    ->isRequired()
                                                    ->validate()
                                                        ->ifNotInArray([
                                                            self::EXECUTION_ENVIRONMENT_HOST,
                                                            self::EXECUTION_ENVIRONMENT_MASTER_CONTAINER,
                                                        ])
                                                        ->thenInvalid('Invalid execution environment %s')
                                                    ->end()
                                                ->end()
                                                ->arrayNode('command')
                                                    ->scalarPrototype()->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                ->end()
                            ->end()

                            ->arrayNode('environments')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')->end()
                                        ->scalarNode('value')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('mounts')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('source')->end()
                                        ->scalarNode('target')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('ports')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('containerPort')->end()
                                        ->scalarNode('hostPort')->end()
                                        ->scalarNode('hostIp')
                                            ->defaultValue('0.0.0.0')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
