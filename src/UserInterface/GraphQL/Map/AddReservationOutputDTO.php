<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Map;

use App\UserInterface\GraphQL\ResolverMap\ResolvedAs;

#[ResolvedAs('AddReservationOutput')]
final readonly class AddReservationOutputDTO
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
