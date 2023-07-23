<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Map;

use App\UserInterface\GraphQL\ResolverMap\ResolvedAs;

#[ResolvedAs('StopServiceOutput')]
final class StopServiceSuccessOutputDTO
{
    public function __construct(
        private bool $success,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
