<?php

declare(strict_types=1);

namespace App\Common\Graphql\Type;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Language\AST\Node;

final class DateTimeType
{
    public static function serialize(DateTimeInterface $value): string
    {
        return $value->format('Y-m-d H:i:s');
    }

    public static function parseValue($value): DateTimeImmutable
    {
        return new DateTimeImmutable($value);
    }

    public static function parseLiteral(Node $valueNode): DateTimeImmutable
    {
        return new DateTimeImmutable($valueNode->value);
    }
}
