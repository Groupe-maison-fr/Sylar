<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

final class ServiceClonerStatusDTO
{
    /**
     * @var string
     */
    private $masterName;

    /**
     * @var string
     */
    private $instanceName;

    /**
     * @var string
     */
    private $containerName;

    /**
     * @var string|null
     */
    private $stateFilename;

    /**
     * @var string|null
     */
    private $zfsPath;

    /**
     * @var bool|null
     */
    private $isMaster;

    /**
     * @var int|null
     */
    private $index;

    /**
     * @var string|null
     */
    private $dockerState;

    public function __construct(
        string $masterName,
        string $instanceName
    ) {
        $this->masterName = $masterName;
        $this->instanceName = $instanceName;
        $this->containerName = null;
        $this->stateFilename = null;
        $this->dockerState = null;
        $this->zfsPath = null;
        $this->isMaster = null;
        $this->index = $instanceName == 'master' ? 0 : null;
    }

    public function getStateFilename(): ?string
    {
        return $this->stateFilename;
    }

    public function setStateFilename(?string $stateFilename): void
    {
        $this->stateFilename = $stateFilename;
    }

    public function getZfsPath(): ?string
    {
        return $this->zfsPath;
    }

    public function setZfsPath(?string $zfsPath): void
    {
        $this->zfsPath = $zfsPath;
    }

    public function isMaster(): ?bool
    {
        return $this->isMaster;
    }

    public function setIsMaster(?bool $isMaster): void
    {
        $this->isMaster = $isMaster;
    }

    public function getDockerState(): ?string
    {
        return $this->dockerState;
    }

    public function setDockerState(?string $dockerState): void
    {
        $this->dockerState = $dockerState;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function setIndex(?int $index): void
    {
        $this->index = $index;
    }

    public function getContainerName(): string
    {
        return $this->containerName;
    }

    public function setContainerName(string $containerName): void
    {
        $this->containerName = $containerName;
    }
}
