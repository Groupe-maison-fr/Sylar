<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

interface ProcessInterface
{
    public function exec(CommandDTO $command): ExecutionResultDTO;

    public function run(?string ...$arguments): ExecutionResultDTO;

    public function mayRun(?string ...$arguments): ExecutionResultDTO;
}
