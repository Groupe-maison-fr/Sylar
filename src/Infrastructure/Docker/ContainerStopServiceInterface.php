<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerStopServiceInterface
{
    public function stop(string $dockerName, string ...$arguments): void;
}
