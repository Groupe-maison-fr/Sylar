<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Infrastructure\Filesystem\FilesystemDTO;

final class ServiceClonerStatusDTO
{
    private bool $isMaster;
    private array $exposedPorts;
    private ?FilesystemDTO $zfsFilesystem;
    private ?string $dockerState;

    public function __construct(
        private string $masterName,
        private string $instanceName,
        private int $index,
        private string $containerName,
        private string $zfsFilesystemName,
        private string $zfsFilesystemPath,
        private ?int $createdAt,
    ) {
        $this->isMaster = $instanceName == ServiceClonerNamingServiceInterface::MASTER_NAME;
        $this->zfsFilesystem = null;
        $this->dockerState = null;
        $this->exposedPorts = [];
    }

    public function toArray(): array
    {
        return [
            'sylar-masterName' => $this->masterName,
            'sylar-instanceName' => $this->instanceName,
            'sylar-index' => sprintf('%d', $this->index),
            'sylar-containerName' => $this->containerName,
            'sylar-zfsFilesystemName' => $this->zfsFilesystemName,
            'sylar-zfsFilesystemPath' => $this->zfsFilesystemPath,
            'sylar-createdAt' => sprintf('%d', $this->createdAt),
        ];
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['sylar-masterName'],
            $data['sylar-instanceName'],
            (int) $data['sylar-index'],
            $data['sylar-containerName'],
            $data['sylar-zfsFilesystemName'],
            $data['sylar-zfsFilesystemPath'],
            (int) $data['sylar-createdAt'],
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
