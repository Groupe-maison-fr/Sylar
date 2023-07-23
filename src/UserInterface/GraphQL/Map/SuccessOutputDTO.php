<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Map;

final readonly class SuccessOutputDTO
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
