<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class ServiceCloner
{
    /**
     * @var string
     */
    private $stateRoot;

    /**
     * @var string
     */
    private $zpoolName;

    /**
     * @var string
     */
    private $zpoolRoot;

    /**
     * @var string
     */
    private $configurationRoot;

    /**
     * @var Service[]|ArrayCollection
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getstateRoot()
    {
        return $this->stateRoot;
    }

    public function setstateRoot(string $stateRoot): void
    {
        $this->stateRoot = $stateRoot;
    }

    public function getZpoolName()
    {
        return $this->zpoolName;
    }

    public function setZpoolName(string $zpoolName): void
    {
        $this->zpoolName = $zpoolName;
    }

    public function getZpoolRoot()
    {
        return $this->zpoolRoot;
    }

    public function setZpoolRoot(string $zpoolRoot): void
    {
        $this->zpoolRoot = $zpoolRoot;
    }

    public function addService(Service $service): void
    {
        $this->services[] = $service;
    }

    /**
     * @return Service[] | ArrayCollection
     */
    public function getServices(): ArrayCollection
    {
        return $this->services;
    }

    public function getServiceByName(string $name): ?Service
    {
        $services = $this->services->filter(function (Service $service) use ($name) {
            return $service->getName() === $name;
        });

        if ($services->isEmpty()) {
            return null;
        }

        return $services->first();
    }

    public function removeService(Service $service): void
    {
        $this->services->removeElement($service);
    }

    public function setConfigurationRoot(string $configurationRoot): void
    {
        $this->configurationRoot = $configurationRoot;
    }

    public function getConfigurationRoot(): string
    {
        return $this->configurationRoot;
    }
}
