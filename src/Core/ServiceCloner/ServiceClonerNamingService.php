<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ServiceClonerNamingService implements ServiceClonerNamingServiceInterface
{
    private const MASTER_NAME = 'master';

    private SluggerInterface $slugger;

    private ConfigurationServiceInterface $dockerConfiguration;

    public function __construct(
        ConfigurationServiceInterface $dockerConfiguration,
        SluggerInterface $slugger
    ) {
        $this->dockerConfiguration = $dockerConfiguration;
        $this->slugger = $slugger;
    }

    public function getZfsFilesystemName(string $masterName, string $instanceName): string
    {
        return sprintf('%s/%s', $this->dockerConfiguration->getConfiguration()->getZpoolName(), $this->getFullName($masterName, $instanceName, '@'));
    }

    public function getZfsFilesystemPath(string $masterName, string $instanceName): string
    {
        return sprintf('%s/%s', $this->dockerConfiguration->getConfiguration()->getZpoolRoot(), $this->getFullName($masterName, $instanceName, '-'));
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
