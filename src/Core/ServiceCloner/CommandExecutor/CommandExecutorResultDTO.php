<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\CommandExecutor;

final class CommandExecutorResultDTO
{
    public function __construct(
        private string $subCommand,
        private array $output,
    ) {
    }

    public function getSubCommand(): string
    {
        return $this->subCommand;
    }

    public function getOutput(): array
    {
        return $this->output;
    }
}
