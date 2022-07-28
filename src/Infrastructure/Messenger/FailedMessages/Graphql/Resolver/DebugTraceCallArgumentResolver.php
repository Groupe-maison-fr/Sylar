<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class DebugTraceCallArgumentResolver implements QueryInterface
{
    public function __invoke(ResolveInfo $info, array $arguments, Argument $args)
    {
        switch ($info->fieldName) {
            case 'type':
                return $arguments[0];
            case 'value':
                return json_encode($arguments[1]);
        }
    }
}
