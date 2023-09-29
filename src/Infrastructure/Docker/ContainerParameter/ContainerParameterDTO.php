<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

final readonly class ContainerParameterDTO
{
    public function __construct(
        public string $name,
        public int $index,
        public string $replicatedFilesystem,
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
