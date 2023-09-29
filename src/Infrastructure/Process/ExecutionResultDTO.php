<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class ExecutionResultDTO
{
    public function __construct(
        private string $stdOutput,
        private string $stdError,
        private int $exitCode,
    ) {
    }

    public function __toString(): string
    {
        return $this->getStdError() . ' ' . $this->getStdOutput();
    }

    public function getStdOutput(): string
    {
        return $this->stdOutput;
    }

    public function getStdError(): string
    {
        return $this->stdError;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
