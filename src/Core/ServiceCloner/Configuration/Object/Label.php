<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final class Label
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
