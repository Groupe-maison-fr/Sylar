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
     * @var Environment[]
     */
    private array $environments = [];

    /**
     * @var Mount[]
     */
    private array $mounts = [];

    /**
     * @var Port[]
     */
    private array $ports = [];

    /**
     * @var Label[]
     */
    private array $labels = [];

    public function __construct()
    {
    }

    public function getLifeCycleHooks(): ?LifeCycleHooks
    {
        return $this->lifeCycleHooks;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getEntryPoint(): ?string
    {
        return $this->entryPoint;
    }

    public function getNetworkMode(): ?string
    {
        return $this->networkMode;
    }

    /**
     * @return ArrayCollection<Environment>
     */
    public function getEnvironments(): ArrayCollection
    {
        return new ArrayCollection($this->environments);
    }

    /**
     * @return ArrayCollection<Mount>
     */
    public function getMounts(): ArrayCollection
    {
        return new ArrayCollection($this->mounts);
    }

    /**
     * @return ArrayCollection<Port>
     */
    public function getPorts(): ArrayCollection
    {
        return new ArrayCollection($this->ports);
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
        return new ArrayCollection($this->labels);
    }

    /** @internal */
    public function setLifeCycleHooks(?LifeCycleHooks $lifeCycleHooks): void
    {
        $this->lifeCycleHooks = $lifeCycleHooks;
    }

    /** @internal */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /** @internal */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /** @internal */
    public function setCommand(?string $command): void
    {
        $this->command = $command;
    }

    /** @internal */
    public function setEntryPoint(?string $entryPoint): void
    {
        $this->entryPoint = $entryPoint;
    }

    /** @internal */
    public function setNetworkMode(?string $networkMode): void
    {
        $this->networkMode = $networkMode;
    }

    /** @internal */
    public function setEnvironments(array $environments): void
    {
        $this->environments = $environments;
    }

    /** @internal */
    public function setMounts(array $mounts): void
    {
        $this->mounts = $mounts;
    }

    /** @internal */
    public function setPorts(array $ports): void
    {
        $this->ports = $ports;
    }

    /** @internal */
    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }
}
