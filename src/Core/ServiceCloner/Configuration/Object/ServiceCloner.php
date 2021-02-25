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
     * @var Service[]|ArrayCollection
     */
    private $services;
    /**
     * @var Command[]|ArrayCollection
     */
    private $commands;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->commands = new ArrayCollection();
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

    public function addCommand(Command $command): void
    {
        $this->commands[] = $command;
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

    /**
     * @return Command[] | ArrayCollection
     */
    public function getCommands(): ArrayCollection
    {
        return $this->commands;
    }

    public function removeCommand(Command $command): void
    {
        $this->commands->removeElement($command);
    }

    public function getCommandByName(string $name): ?Command
    {
        $commands = $this->commands->filter(fn (Command $command) => $command->getName() === $name);

        if ($commands->isEmpty()) {
            return null;
        }

        return $commands->first();
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
