<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerImageServiceInterface
{
    public function imageExists(string $imageName): bool;

    public function pullImage(string $imageName): bool;
}
