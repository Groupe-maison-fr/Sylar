<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final class Port
{
    private string $containerPort;
    private string $hostPort;
    private string $hostIp;

    public function getContainerPort(): string
    {
        return $this->containerPort;
    }

    public function setContainerPort(string $containerPort): void
    {
        $this->containerPort = $containerPort;
    }

    public function getHostPort(): string
    {
        return $this->hostPort;
    }

    public function setHostPort(string $hostPort): void
    {
        $this->hostPort = $hostPort;
    }

    public function getHostIp(): ?string
    {
        return $this->hostIp;
    }

    public function setHostIp(?string $hostIp): void
    {
        $this->hostIp = $hostIp;
    }
}
