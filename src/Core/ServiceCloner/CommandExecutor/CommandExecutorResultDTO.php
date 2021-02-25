<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\CommandExecutor;

final class CommandExecutorResultDTO
{
    private string $subCommand;

    private array $output;

    public function __construct(string $subCommand, array $output)
    {
        $this->subCommand = $subCommand;
        $this->output = $output;
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
