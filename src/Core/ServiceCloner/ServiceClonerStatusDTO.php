<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Infrastructure\Filesystem\FilesystemDTO;

final class ServiceClonerStatusDTO
{
    private string $masterName;
    private string $instanceName;
    private int $index;
    private string $containerName;
    private string $zfsFilesystemName;
    private string $zfsFilesystemPath;
    private bool $isMaster;
    private array $exposedPorts;
    private ?FilesystemDTO $zfsFilesystem;
    private ?string $dockerState;
    private ?int $createdAt;

    public function __construct(
        string $masterName,
        string $instanceName,
        int $index,
        string $containerName,
        string $zfsFilesystemName,
        string $zfsFilesystemPath,
        int $createdAt
    ) {
        $this->masterName = $masterName;
        $this->instanceName = $instanceName;
        $this->index = $index;
        $this->containerName = $containerName;
        $this->isMaster = $instanceName == 'master';
        $this->zfsFilesystemName = $zfsFilesystemName;
        $this->zfsFilesystemPath = $zfsFilesystemPath;
        $this->createdAt = $createdAt;
        $this->zfsFilesystem = null;
        $this->dockerState = null;
        $this->exposedPorts = [];
    }

    public function toArray(): array
    {
        return [
            'masterName' => $this->masterName,
            'instanceName' => $this->instanceName,
            'index' => $this->index,
            'containerName' => $this->containerName,
            'zfsFilesystemName' => $this->zfsFilesystemName,
            'zfsFilesystemPath' => $this->zfsFilesystemPath,
            'createdAt' => $this->createdAt,
            'isMaster' => $this->isMaster,
        ];
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['masterName'],
            $data['instanceName'],
            (int) $data['index'],
            $data['containerName'],
            $data['zfsFilesystemName'],
            $data['zfsFilesystemPath'],
            (int) $data['createdAt']
        );
    }

    public function isMaster(): bool
    {
        return $this->isMaster;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }

    public function getContainerName(): string
    {
        return $this->containerName;
    }

    public function setContainerName(string $containerName): void
    {
        $this->containerName = $containerName;
    }

    public function getMasterName(): string
    {
        return $this->masterName;
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    public function getZfsFilesystemName(): ?string
    {
        return $this->zfsFilesystemName;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function getDockerState(): ?string
    {
        return $this->dockerState;
    }

    public function setDockerState(?string $dockerState): void
    {
        $this->dockerState = $dockerState;
    }

    public function getExposedPorts(): array
    {
        return $this->exposedPorts;
    }

    public function setExposedPorts(array $exposedPorts): void
    {
        $this->exposedPorts = $exposedPorts;
    }

    public function setZfsFilesystem(?FilesystemDTO $zfsFilesystem): void
    {
        $this->zfsFilesystem = $zfsFilesystem;
    }

    public function getZfsFilesystem(): ?FilesystemDTO
    {
        return $this->zfsFilesystem;
    }
}
