<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerExecServiceInterface
{
    public function exec(string $dockerName, string ...$arguments): string;
}
