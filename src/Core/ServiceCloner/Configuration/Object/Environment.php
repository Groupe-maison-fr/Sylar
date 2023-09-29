<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class Environment
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
