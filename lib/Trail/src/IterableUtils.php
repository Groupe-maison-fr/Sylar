<?php

declare(strict_types=1);

namespace Micoli\Trail;

class IterableUtils
{
    public static function first(mixed $value): mixed
    {
        if (!is_iterable($value)) {
            throw new TrailException(sprintf('Can not execute "first" on non iterable value "%s"', json_encode($value)));
        }

        return $value[array_key_first($value)];
    }

    public static function last(mixed $value): mixed
    {
        if (!is_iterable($value)) {
            throw new TrailException(sprintf('Can not execute "last" on non iterable value "%s"', json_encode($value)));
        }

        return $value[array_key_last($value)];
    }

    public static function count(mixed $value): int
    {
        if (!is_iterable($value)) {
            throw new TrailException(sprintf('Can not execute "count" on non iterable value "%s"', json_encode($value)));
        }

        return count($value);
    }
}
