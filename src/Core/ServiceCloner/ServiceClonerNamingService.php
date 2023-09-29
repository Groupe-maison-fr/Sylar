<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ServiceClonerNamingService implements ServiceClonerNamingServiceInterface
{
    public function __construct(
        private ConfigurationServiceInterface $dockerConfiguration,
        private SluggerInterface $slugger,
    ) {
    }

    public function getZfsFilesystemName(string $masterName, string $instanceName): string
    {
        return sprintf('%s/%s', $this->dockerConfiguration->getConfiguration()->zpoolName, $this->getFullName($masterName, $instanceName, '@'));
    }

    public function getZfsFilesystemPath(string $masterName, string $instanceName): string
    {
        return sprintf('%s/%s', $this->dockerConfiguration->getConfiguration()->zpoolRoot, $this->getFullName($masterName, $instanceName, '-'));
    }

    public function getFullName(string $masterName, string $instanceName, string $separator): string
    {
        $masterNameSlug = $this->slugger->slug($masterName)->toString();
        $instanceNameSlug = $this->slugger->slug($instanceName)->toString();

        return $this->isMasterName($instanceName) ? $masterNameSlug : sprintf('%s%s%s', $masterNameSlug, $separator, $instanceNameSlug);
    }

    public function getDockerName(string $masterName, string $instanceName): string
    {
        return $this->getFullName($masterName, $instanceName, '_');
    }

    public function isMasterName(string $instanceName): bool
    {
        return $instanceName === self::MASTER_NAME;
    }
}
