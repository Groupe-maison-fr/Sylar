<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class CommandDTO
{
    private array $arguments;
    private bool $mustRun;
    private bool $sudo;

    public function __construct(
        array $arguments,
        bool $mustRun,
        bool $sudo
    ) {
        $this->arguments = $arguments;
        $this->sudo = $sudo;
        $this->mustRun = $mustRun;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function mayRun(): bool
    {
        return !$this->mustRun;
    }

    public function mustRun(): bool
    {
        return $this->mustRun;
    }

    public function sudo(): bool
    {
        return $this->sudo;
    }
}
