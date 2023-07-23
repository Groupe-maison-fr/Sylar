<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\ResolverMap;

use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\SuccessOutputDTO;
use Overblog\GraphQLBundle\Resolver\ResolverMap as ResolverMapParent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ResolverMap extends ResolverMapParent
{
    /** @param array{string:class-string[]} $resoledAsMap */
    public function __construct(
        #[Autowire('%graphql_resolved_as%')]
        private readonly array $resoledAsMap,
    ) {
    }

    protected function map(): mixed
    {
        return array_map($this->resolveAsSuccess(...), $this->resoledAsMap);
    }

    /**
     * @param class-string[] $classNames
     *
     * @return array{'%%resolveType': callable(mixed): ?string}
     */
    public function resolveAsSuccess(array $classNames): array
    {
        return [
            self::RESOLVE_TYPE => function ($value) use ($classNames): ?string {
                if ($value instanceof FailedOutputDTO) {
                    return 'FailedOutput';
                }

                if ($value instanceof SuccessOutputDTO) {
                    return 'SuccessOutput';
                }
                foreach ($classNames as $className) {
                    if (is_a($value, $className)) {
                        return 'SuccessOutput';
                    }
                }

                return null;
            },
        ];
    }
}
