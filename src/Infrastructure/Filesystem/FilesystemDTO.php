<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

final class FilesystemDTO
{
    public function __construct(
        private string $name,
        private int $available,
        private int $used,
        private int $usedBySnapshot,
        private int $usedByDataset,
        private int $usedByRefReservation,
        private int $usedByChild,
        private int $refer,
        private string $mountPoint,
        private string $origin,
        private string $type,
        private int $creationTimestamp,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function getUsed(): int
    {
        return $this->used;
    }

    public function getUsedBySnapshot(): int
    {
        return $this->usedBySnapshot;
    }

    public function getUsedByDataset(): int
    {
        return $this->usedByDataset;
    }

    public function getUsedByRefReservation(): int
    {
        return $this->usedByRefReservation;
    }

    public function getUsedByChild(): int
    {
        return $this->usedByChild;
    }

    public function getRefer(): int
    {
        return $this->refer;
    }

    public function getMountPoint(): string
    {
        return $this->mountPoint;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreationTimestamp(): int
    {
        return $this->creationTimestamp;
    }
}
