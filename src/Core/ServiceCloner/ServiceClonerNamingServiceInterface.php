<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface ServiceClonerNamingServiceInterface
{
    public function getZfsFilesystemName(string $masterName, string $instanceName): string;

    public function getZfsFilesystemPath(string $masterName, string $instanceName): string;

    public function getFullName(string $masterName, string $instanceName, string $separator): string;

    public function getDockerName(string $masterName, string $instanceName): string;

    public function isMasterName(string $instanceName): bool;
}
