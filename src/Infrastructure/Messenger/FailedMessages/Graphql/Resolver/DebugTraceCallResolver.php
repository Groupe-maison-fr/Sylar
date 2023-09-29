<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Resolver;

use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Throwable;

final class DebugTraceCallResolver implements QueryInterface
{
    /**
     * @param mixed[] $debugTraceCall
     */
    public function __invoke(ResolveInfo $info, array $debugTraceCall, Argument $args): mixed
    {
        switch ($info->fieldName) {
            case 'namespace':
                return $debugTraceCall['namespace'];
            case 'short_class':
                return $debugTraceCall['short_class'];
            case 'class':
                return $debugTraceCall['class'];
            case 'type':
                return $debugTraceCall['type'];
            case 'function':
                return $debugTraceCall['function'];
            case 'file':
                return $debugTraceCall['file'];
            case 'line':
                return $debugTraceCall['line'];
            case 'arguments':
                try {
                    return $this->createArgumentsTree($debugTraceCall['args']);
                } catch (Throwable $e) {
                    return [new DebugTraceCallArgumentDTO(
                        'Error on decoding arguments',
                        $e->getMessage(),
                    )];
                }
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    private function createArgumentsTree(mixed $arguments): mixed
    {
        if (!is_array($arguments)) {
            return $arguments;
        }

        if (count($arguments) === 2 && in_array($arguments[0], ['incomplete-object', 'object', 'null', 'boolean', 'integer', 'float', 'resource', 'string'])) {
            return new DebugTraceCallArgumentDTO(
                $arguments[0],
                $this->createArgumentsTree($arguments[1]),
            );
        }

        if (count($arguments) === 2 && $arguments[0] === 'array') {
            return array_map($this->createArgumentsTree(...), $arguments[1]);
        }

        return array_map($this->createArgumentsTree(...), $arguments);
    }
}
