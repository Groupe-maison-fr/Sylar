<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Map;

final class FailedOutputDTO
{
    private int $code;

    private string $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
