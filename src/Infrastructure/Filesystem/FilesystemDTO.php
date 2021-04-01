<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

final class FilesystemDTO
{
    private string $name;
    private int $available;
    private int $used;
    private int $usedBySnapshot;
    private int $usedByDataset;
    private int $usedByRefReservation;
    private int $usedByChild;
    private int $refer;
    private string $mountPoint;
    private string $origin;
    private string $type;

    public function __construct(
        string $name,
        int $available,
        int $used,
        int $usedBySnapshot,
        int $usedByDataset,
        int $usedByRefReservation,
        int $usedByChild,
        int $refer,
        string $mountPoint,
        string $origin,
        string $type
    ) {
        $this->name = $name;
        $this->available = $available;
        $this->used = $used;
        $this->usedBySnapshot = $usedBySnapshot;
        $this->usedByDataset = $usedByDataset;
        $this->usedByRefReservation = $usedByRefReservation;
        $this->usedByChild = $usedByChild;
        $this->refer = $refer;
        $this->mountPoint = $mountPoint;
        $this->origin = $origin;
        $this->type = $type;
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
}
