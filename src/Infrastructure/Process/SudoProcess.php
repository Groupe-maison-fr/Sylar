<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

final class SudoProcess implements ProcessInterface
{
    private Process $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function run(...$arguments): string
    {
        return $this->process->run('sudo', ...$arguments);
    }

    public function mayRun(...$arguments): string
    {
        return $this->process->mayRun('sudo', ...$arguments);
    }
}
