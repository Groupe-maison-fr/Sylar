<?php

declare(strict_types=1);

namespace App\Infrastructure\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConsoleCommandFilterCompilerPass implements CompilerPassInterface
{
    private $filteredNamespaces;

    public function __construct()
    {
        $this->filteredNamespaces = ['Doctrine', 'Symfony', 'Overblog'];
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $attributes) {
            $definition = $container->getDefinition($id);
            foreach ($this->filteredNamespaces as $filteredNamespace) {
                if (strpos($definition->getClass(), $filteredNamespace) === 0) {
                    $this->removeCommand($container, $id);
                }
            }
        }
    }

    private function removeCommand(ContainerBuilder $container, $id): void
    {
        $definition = $container->getDefinition($id);
        $tags = $definition->getTags();
        unset($tags['console.command']);
        $definition->setTags($tags);
    }
}
