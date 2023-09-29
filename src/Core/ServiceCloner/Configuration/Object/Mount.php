<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class Mount
{
    public function __construct(
        public string $source,
        public string $target,
    ) {
    }
}
