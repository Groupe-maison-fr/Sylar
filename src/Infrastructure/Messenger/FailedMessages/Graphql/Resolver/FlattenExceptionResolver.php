<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class FlattenExceptionResolver implements QueryInterface
{
    public function __invoke(ResolveInfo $info, FlattenException $flattenException, Argument $args)
    {
        switch ($info->fieldName) {
            case 'message':
                return $flattenException->getMessage();
            case 'code':
                return $flattenException->getCode();
            case 'previous':
                return $flattenException->getPrevious();
            case 'trace':
                return $flattenException->getTrace();
            case 'traceAsString':
                return $flattenException->getTraceAsString();
            case 'class':
                return $flattenException->getClass();
            case 'statusCode':
                return $flattenException->getStatusCode();
            case 'statusText':
                return $flattenException->getStatusText();
            case 'headers':
                return $flattenException->getHeaders();
            case 'file':
                return $flattenException->getFile();
            case 'line':
                return $flattenException->getLine();
            case 'asString':
                return $flattenException->getAsString();
        }
    }
}
