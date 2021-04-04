<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class CommandDTO
{
    private array $arguments;
    private bool $mayRun;
    private bool $sudo;

    public function __construct(
        array $arguments,
        bool $mayRun,
        bool $sudo
    ) {
        $this->arguments = $arguments;
        $this->mayRun = $mayRun;
        $this->sudo = $sudo;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function isMayRun(): bool
    {
        return $this->mayRun;
    }

    public function isSudo(): bool
    {
        return $this->sudo;
    }
}
