<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerStateServiceInterface
{
    public function dockerState(string $dockerName): ?string;
}
