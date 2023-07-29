<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Resolver;

final readonly class DebugTraceCallArgumentDTO
{
    public function __construct(
        public string $type,
        public mixed $value,
    ) {
    }
}
