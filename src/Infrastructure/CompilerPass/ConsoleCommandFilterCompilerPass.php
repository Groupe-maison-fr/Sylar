<?php

declare(strict_types=1);

namespace App\Infrastructure\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConsoleCommandFilterCompilerPass implements CompilerPassInterface
{
    private array $excluded;

    private array $whitelisted;

    public function __construct()
    {
        $this->excluded = ['^Doctrine', '^Symfony', '^Overblog'];
        $this->whitelisted = ['.*\\\\Cache.*Command$', 'Messenger', '^App', 'Doctrine\\\\Migrations\\\\Tools\\\\Console\\\\Command\\\\StatusCommand'];
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $attributes) {
            $class = $container->getDefinition($id)->getClass();
            if ($this->pregMatchPatternArray($this->excluded, $class) &&
                !$this->pregMatchPatternArray($this->whitelisted, $class)) {
                $this->removeCommand($container, $id);
            }
        }
    }

    private function pregMatchPatternArray(array $regularExpressions, string $className): bool
    {
        foreach ($regularExpressions as $regularExpression) {
            if (preg_match('!' . $regularExpression . '!', $className)) {
                return true;
            }
        }

        return false;
    }

    private function removeCommand(ContainerBuilder $container, $id): void
    {
        $definition = $container->getDefinition($id);
        $tags = $definition->getTags();
        unset($tags['console.command']);
        $definition->setTags($tags);
    }
}
