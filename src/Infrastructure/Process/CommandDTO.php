<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class CommandDTO
{
    /**
     * @param string[] $arguments
     */
    public function __construct(
        private array $arguments,
        private bool $mustRun,
        private bool $sudo,
    ) {
    }

    /**
     * @return string[]
     */
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
