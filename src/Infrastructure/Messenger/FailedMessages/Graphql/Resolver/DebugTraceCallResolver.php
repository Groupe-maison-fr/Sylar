<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use stdClass;

final class DebugTraceCallResolver implements QueryInterface
{
    public function __invoke(ResolveInfo $info, array $debugTraceCall, Argument $args)
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
                return json_encode($this->createArgumentsTree($debugTraceCall['args']));
        }
    }

    private function createArgumentsTree($arguments)
    {
        if (!is_array($arguments)) {
            return $arguments;
        }

        if (count($arguments) === 2 && in_array($arguments[0], ['incomplete-object', 'object', 'null', 'boolean', 'integer', 'float', 'resource', 'string'])) {
            $output = new stdClass();
            $output->type = $arguments[0];
            $output->value = $this->createArgumentsTree($arguments[1]);

            return $output;
        }

        if (count($arguments) === 2 && $arguments[0] === 'array') {
            return array_map(fn (array $argument) => $this->createArgumentsTree($argument), $arguments[1]);
        }

        return array_map(fn ($argument) => $this->createArgumentsTree($argument), $arguments);
    }
}
