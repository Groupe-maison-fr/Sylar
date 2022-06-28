<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class Service
{
    private string $name = '';
    private string $image = '';
    private string $command = '';
    private ?string $entryPoint = null;
    private ?string $networkMode = null;

    /**
     * @var LifeCycleHooks|null
     */
    private $lifeCycleHooks;

    /**
     * @var ArrayCollection<Environment>
     */
    private $environments;

    /**
     * @var ArrayCollection<Mount>
     */
    private $mounts;

    /**
     * @var ArrayCollection<Port>
     */
    private $ports;

    /**
     * @var ArrayCollection<Label>
     */
    private $labels;

    public function __construct()
    {
        $this->environments = new ArrayCollection();
        $this->mounts = new ArrayCollection();
        $this->ports = new ArrayCollection();
        $this->labels = new ArrayCollection();
    }

    public function getLifeCycleHooks(): ?LifeCycleHooks
    {
        return $this->lifeCycleHooks;
    }

    public function setLifeCycleHooks(?LifeCycleHooks $lifeCycleHooks): void
    {
        $this->lifeCycleHooks = $lifeCycleHooks;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(?string $command): void
    {
        $this->command = $command;
    }

    public function getEntryPoint(): ?string
    {
        return $this->entryPoint;
    }

    public function setEntryPoint(?string $entryPoint): void
    {
        $this->entryPoint = $entryPoint;
    }

    public function getNetworkMode(): ?string
    {
        return $this->networkMode;
    }

    public function setNetworkMode(?string $networkMode): void
    {
        $this->networkMode = $networkMode;
    }

    public function addEnvironment(Environment $environment): void
    {
        $this->environments[] = $environment;
    }

    /**
     * @return ArrayCollection<Environment>
     */
    public function getEnvironments(): ArrayCollection
    {
        return $this->environments;
    }

    public function removeEnvironment(Environment $environment): void
    {
        $this->environments->removeElement($environment);
    }

    public function addMount(Mount $mount): void
    {
        $this->mounts[] = $mount;
    }

    /**
     * @return ArrayCollection<Mount>
     */
    public function getMounts(): ArrayCollection
    {
        return $this->mounts;
    }

    public function removeMount(Mount $mount): void
    {
        $this->mounts->removeElement($mount);
    }

    public function addPort(Port $port): void
    {
        $this->ports[] = $port;
    }

    /**
     * @return ArrayCollection<Port>
     */
    public function getPorts(): ArrayCollection
    {
        return $this->ports;
    }

    public function removePort(Port $port): void
    {
        $this->ports->removeElement($port);
    }

    public function addLabel(Label $Label): void
    {
        $this->labels[] = $Label;
    }

    /**
     * @return ArrayCollection<Label>
     */
    public function getLabels(): ArrayCollection
    {
        return $this->labels;
    }

    public function removeLabel(Label $Label): void
    {
        $this->labels->removeElement($Label);
    }
}
