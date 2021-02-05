<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

final class ContainerParameterDTO
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $replicatedFilesystem;

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
