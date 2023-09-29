<?php

declare(strict_types=1);

namespace Micoli\Trail\tests\Fixtures;

class Foo
{
    /**
     * @param Bar[] $bars
     */
    public function __construct(
        public readonly int $count,
        public readonly string $name,
        public readonly array $bars,
    ) {
    }
}
