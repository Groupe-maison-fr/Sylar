<?php

declare(strict_types=1);

namespace App\Infrastructure\CompilerPass;

use App\UserInterface\GraphQL\ResolverMap\ResolvedAs;
use HaydenPierce\ClassFinder\ClassFinder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class GraphQLResolverCompilerPass implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private array $dtoNamespaces;

    public function __construct()
    {
        $this->dtoNamespaces = ['App\\UserInterface\\GraphQL\\Map'];
    }

    public function process(ContainerBuilder $container): void
    {
        $map = [];
        foreach ($this->dtoNamespaces as $dtoNamespace) {
            $classes = ClassFinder::getClassesInNamespace($dtoNamespace, ClassFinder::RECURSIVE_MODE);
            foreach ($classes as $class) {
                $map = $this->getGraphqlMappedOutput($class, $map);
            }
        }
        $container->setParameter('graphql_resolved_as', $map);
    }

    /**
     * @param array{string: class-string[]} $map
     *
     * @return array{string: class-string[]}
     */
    public function getGraphqlMappedOutput(string $class, array $map): array
    {
        $reflectedClass = new ReflectionClass($class);

        foreach ($reflectedClass->getAttributes(ResolvedAs::class) as $reflectedAttribute) {
            /** @var ResolvedAs $attribute */
            $attribute = $reflectedAttribute->newInstance();
            if (!isset($map[$attribute->resolvedType])) {
                $map[$attribute->resolvedType] = [];
            }
            $map[$attribute->resolvedType][] = $class;
        }

        return $map;
    }
}
