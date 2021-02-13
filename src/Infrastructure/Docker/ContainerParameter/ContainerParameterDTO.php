<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

final class ContainerParameterDTO
{
    private int $index;
    private string $name;
    private string $replicatedFilesystem;

    public function __construct(
        string $name,
        int $index,
        string $replicatedFilesystem
    ) {
        $this->index = $index;
        $this->name = $name;
        $this->replicatedFilesystem = $replicatedFilesystem;
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
