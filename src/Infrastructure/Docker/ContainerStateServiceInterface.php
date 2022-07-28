<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerStateServiceInterface
{
    public function dockerState(string $dockerName): ?string;

    public function dockerExposedPorts(string $dockerName): ?array;
}
