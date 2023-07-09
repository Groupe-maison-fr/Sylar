<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

final class ContainerParameterDTO
{
    public function __construct(
        private string $name,
        private int $index,
        private string $replicatedFilesystem,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getReplicatedFilesystem(): string
    {
        return $this->replicatedFilesystem;
    }
}
