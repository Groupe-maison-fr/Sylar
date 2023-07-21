<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final readonly class ServiceCloner
{
    public function __construct(
        public string $stateRoot,
        public string $zpoolName,
        public string $zpoolRoot,
        public string $configurationRoot,
        /** @var Service[] */
        public array $services = [],
        /** @var Command[] */
        public array $commands = [],
    ) {
    }

    public function getstateRoot(): string
    {
        return $this->stateRoot;
    }

    public function getZpoolName(): string
    {
        return $this->zpoolName;
    }

    public function getZpoolRoot(): string
    {
        return $this->zpoolRoot;
    }

    /**
     * @return ArrayCollection<int, Service>
     */
    public function getServices(): ArrayCollection
    {
        return new ArrayCollection($this->services);
    }

    public function getServiceByName(string $name): ?Service
    {
        $services = (new ArrayCollection($this->services))->filter(fn (Service $service) => $service->name === $name);

        if ($services->isEmpty()) {
            return null;
        }

        return $services->first();
    }

    public function getCommandByName(string $name): ?Command
    {
        $commands = (new ArrayCollection($this->commands))->filter(fn (Command $command) => $command->name === $name);

        if ($commands->isEmpty()) {
            return null;
        }

        return $commands->first();
    }
}
