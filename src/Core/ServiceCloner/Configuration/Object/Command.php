<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class Command
{
    public function __construct(
        public string $name,
        /** @var string[] */
        public array $subCommands = [],
    ) {
    }
}
