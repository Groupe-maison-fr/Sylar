<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\CommandExecutor;

final class CommandExecutorResultDTO
{
    /**
     * @param string[] $output
     */
    public function __construct(
        private string $subCommand,
        private array $output,
    ) {
    }

    public function getSubCommand(): string
    {
        return $this->subCommand;
    }

    /**
     * @return string[]
     */
    public function getOutput(): array
    {
        return $this->output;
    }
}
