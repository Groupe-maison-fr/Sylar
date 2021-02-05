<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

interface ProcessInterface
{
    public function run(...$arguments): string;

    public function mayRun(...$arguments): string;
}
