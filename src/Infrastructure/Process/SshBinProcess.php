<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class SshBinProcess implements ProcessInterface
{
    private Process $process;
    private string $host;
    private string $user;
    private int $port;
    private string $identityFile;

    public function __construct(
        Process $process,
        string $host,
        string $user,
        int $port,
        string $identityFile
    ) {
        $this->process = $process;
        $this->host = $host;
        $this->user = $user;
        $this->port = $port;
        $this->identityFile = $identityFile;
    }

    private function getArguments(string ...$arguments): array
    {
        return array_filter(array_merge([
            'ssh',
            sprintf('%s@%s', $this->user, $this->host),
            '-p', $this->port,
            '-i', $this->identityFile,
            'sudo',
        ], $arguments));
    }

    public function exec(CommandDTO $command): ExecutionResultDTO
    {
        return $this->process->exec(new CommandDTO(
            $this->getArguments(...$command->getArguments()),
            $command->mustRun(),
            $command->sudo()
        ));
    }

    public function run(?string ...$arguments): ExecutionResultDTO
    {
        return $this->process->exec(new CommandDTO(
            $this->getArguments(...$arguments),
            true,
            true
        ));
    }

    public function mayRun(?string ...$arguments): ExecutionResultDTO
    {
        return $this->process->exec(new CommandDTO(
            $this->getArguments(...$arguments),
            false,
            true
        ));
    }
}
