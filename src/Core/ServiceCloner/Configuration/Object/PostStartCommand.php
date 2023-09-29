<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class PostStartCommand
{
    public function __construct(
        public string $executionEnvironment,
        /** @var string[] */
        public array $command = [],
        public ?string $when = null,
    ) {
    }
}
