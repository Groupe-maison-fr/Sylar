<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\CommandExecutor;

use App\Core\ServiceCloner\Configuration\Object\Command;
use App\Infrastructure\Process\Process;

final class CommandExecutor implements CommandExecutorInterface
{
    private Process $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @return CommandExecutorResultDTO[]
     */
    public function run(Command $command): array
    {
        return $command->getSubCommands()->map(function (string $subCommand) {
            return new CommandExecutorResultDTO(
                $subCommand,
                array_filter(explode(PHP_EOL, $this->process->run('bash', '-c', $subCommand))),
            );
        })->toArray();
    }
}
