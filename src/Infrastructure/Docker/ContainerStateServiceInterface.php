<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerStateServiceInterface
{
    public function dockerState(string $dockerName): ?string;

    /**
     * @return int[]|null
     */
    public function dockerExposedPorts(string $dockerName): ?array;
}
