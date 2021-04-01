<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Map;

final class SuccessOutputDTO
{
    private bool $success;

    public function __construct(bool $success)
    {
        $this->success = $success;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
