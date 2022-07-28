<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

interface ContainerDeleteServiceInterface
{
    public function delete(string $containerName): void;
}
