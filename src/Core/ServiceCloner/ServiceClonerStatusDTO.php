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
     * @var string|null
     */
    private $dockerState;

    public function __construct(
        string $masterName,
        string $instanceName
    ) {
        $this->masterName = $masterName;
        $this->instanceName = $instanceName;
        $this->stateFilename = null;
        $this->dockerState = null;
        $this->zfsPath = null;
        $this->isMaster = null;
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
}
