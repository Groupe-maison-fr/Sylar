<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class ExecutionResultDTO
{
    private array $stdOutput;
    private array $stdError;
    private int $exitCode;

    public function __construct(
        array $stdOutput,
        array $stdError,
        int $exitCode
    ) {
        $this->stdOutput = $stdOutput;
        $this->stdError = $stdError;
        $this->exitCode = $exitCode;
    }

    public function getStdOutput(): array
    {
        return $this->stdOutput;
    }

    public function getStdError(): array
    {
        return $this->stdError;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
