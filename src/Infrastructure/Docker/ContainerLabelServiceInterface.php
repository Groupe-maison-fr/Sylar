<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerLabelServiceInterface
{
    public function getDockerLabelsByName(string $dockerName): array;
}
