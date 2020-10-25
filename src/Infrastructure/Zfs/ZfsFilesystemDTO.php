<?php

declare(strict_types=1);

namespace App\Infrastructure\Zfs;

final class ZfsFilesystemDTO
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $available;

    /**
     * @var string
     */
    private $used;

    /**
     * @var string
     */
    private $usedBySnapshot;

    /**
     * @var string
     */
    private $usedByDataset;

    /**
     * @var string
     */
    private $usedByRefreservation;

    /**
     * @var string
     */
    private $usedByChild;

    /**
     * @var string
     */
    private $refer;

    /**
     * @var string
     */
    private $mountPoint;

    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        string $name,
        string $available,
        string $used,
        string $usedBySnapshot,
        string $usedByDataset,
        string $usedByRefreservation,
        string $usedByChild,
        string $refer,
        string $mountPoint,
        string $origin,
        string $type
    ) {
        $this->name = $name;
        $this->available = $available;
        $this->used = $used;
        $this->usedBySnapshot = $usedBySnapshot;
        $this->usedByDataset = $usedByDataset;
        $this->usedByRefreservation = $usedByRefreservation;
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

    public function getAvailable(): string
    {
        return $this->available;
    }

    public function getUsed(): string
    {
        return $this->used;
    }

    public function getUsedBySnapshot(): string
    {
        return $this->usedBySnapshot;
    }

    public function getUsedByDataset(): string
    {
        return $this->usedByDataset;
    }

    public function getUsedByRefreservation(): string
    {
        return $this->usedByRefreservation;
    }

    public function getUsedByChild(): string
    {
        return $this->usedByChild;
    }

    public function getRefer(): string
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
