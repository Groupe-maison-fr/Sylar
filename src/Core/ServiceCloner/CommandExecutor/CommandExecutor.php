<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\CommandExecutor;

use App\Core\ServiceCloner\Configuration\Object\Command;
use App\Infrastructure\Process\Process;
use Doctrine\Common\Collections\ArrayCollection;

final class CommandExecutor implements CommandExecutorInterface
{
    public function __construct(
        private Process $process,
    ) {
    }

    public function run(Command $command): array
    {
        return (new ArrayCollection($command->subCommands))->map(function (string $subCommand) {
            return new CommandExecutorResultDTO(
                $subCommand,
                array_filter(explode(PHP_EOL, $this->process->run('bash', '-c', $subCommand)->getStdOutput())),
            );
        })->toArray();
    }
}
