<?php

declare(strict_types=1);

namespace Micoli\Trail;

use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class Trail
{
    private PropertyAccessor $propertyAccessor;

    private function __construct(private readonly mixed $value)
    {
        $this->propertyAccessor = new PropertyAccessor();
    }

    public static function eval(mixed $value, string|array $path): mixed
    {
        return Trail::create($value)->path($path)->get();
    }

    public static function create(mixed $value): Trail
    {
        return new Trail($value);
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function path(string|array $path): Trail
    {
        $value = $this->value;
        foreach (is_string($path) ? explode('|', $path) : $path as $subPath) {
            $value = match ($subPath) {
                '@first' => IterableUtils::first($value),
                '@last' => IterableUtils::last($value),
                '@count' => IterableUtils::count($value),
                default => $this->getValue($value, $subPath)
            };
        }

        return Trail::create($value);
    }

    public function first(): Trail
    {
        return self::create(IterableUtils::first($this->value));
    }

    public function last(): Trail
    {
        return self::create(IterableUtils::last($this->value));
    }

    private function getValue(mixed $value, mixed $subPath): mixed
    {
        try {
            return $this->propertyAccessor->getValue($value, $subPath);
        } catch (InvalidPropertyPathException $exception) {
            throw new TrailException($exception->getMessage(), previous: $exception);
        }
    }
}
