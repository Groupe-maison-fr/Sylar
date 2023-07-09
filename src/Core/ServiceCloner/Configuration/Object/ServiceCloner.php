<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class ServiceCloner
{
    private string $stateRoot;
    private string $zpoolName;
    private string $zpoolRoot;
    private string $configurationRoot;

    /**
     * @var Service[]
     */
    private array $services = [];
    /**
     * @var Command[]
     */
    private array $commands = [];

    public function __construct()
    {
    }

    public function getstateRoot()
    {
        return $this->stateRoot;
    }

    public function getZpoolName()
    {
        return $this->zpoolName;
    }

    public function getZpoolRoot()
    {
        return $this->zpoolRoot;
    }

    /**
     * @return ArrayCollection<Service>
     */
    public function getServices(): ArrayCollection
    {
        return new ArrayCollection($this->services);
    }

    public function getServiceByName(string $name): ?Service
    {
        $services = $this->getServices()->filter(fn (Service $service) => $service->getName() === $name);

        if ($services->isEmpty()) {
            return null;
        }

        return $services->first();
    }

    /**
     * @return ArrayCollection<Command>
     */
    public function getCommands(): ArrayCollection
    {
        return new ArrayCollection($this->commands);
    }

    public function getCommandByName(string $name): ?Command
    {
        $commands = $this->getCommands()->filter(fn (Command $command) => $command->getName() === $name);

        if ($commands->isEmpty()) {
            return null;
        }

        return $commands->first();
    }

    public function getConfigurationRoot(): string
    {
        return $this->configurationRoot;
    }

    /** @internal */
    public function setstateRoot(string $stateRoot): void
    {
        $this->stateRoot = $stateRoot;
    }

    /** @internal */
    public function setZpoolName(string $zpoolName): void
    {
        $this->zpoolName = $zpoolName;
    }

    /** @internal */
    public function setZpoolRoot(string $zpoolRoot): void
    {
        $this->zpoolRoot = $zpoolRoot;
    }

    /** @internal */
    public function setConfigurationRoot(string $configurationRoot): void
    {
        $this->configurationRoot = $configurationRoot;
    }

    /** @internal */
    public function setServices(array $services): void
    {
        $this->services = $services;
    }

    /** @internal */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }
}
