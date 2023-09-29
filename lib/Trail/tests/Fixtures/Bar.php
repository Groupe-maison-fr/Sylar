<?php

declare(strict_types=1);

namespace Micoli\Trail\tests\Fixtures;

class Bar
{
    public function __construct(
        public readonly int $number,
        public readonly string $name,
        public readonly string $first,
        public readonly string $last,
    ) {
    }
}
