<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class PostStartWaiter
{
    public function __construct(
        public string $type,
        public string $expression,
        public int $timeout,
    ) {
    }
}
