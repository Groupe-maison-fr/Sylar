<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class LifeCycleHooks
{
    /**
     * @var PreStartCommand[]
     */
    private array $preStartCommands = [];

    /**
     * @var PostStartWaiter[]
     */
    private array $postStartWaiters = [];

    /**
     * @var PostStartCommand[]
     */
    private array $postStartCommands = [];

    /**
     * @var PostDestroyCommand[]
     */
    private array $postDestroyCommands = [];

    public function __construct()
    {
    }

    /**
     * @return ArrayCollection<PreStartCommand>
     */
    public function getPreStartCommands(): ArrayCollection
    {
        return new ArrayCollection($this->preStartCommands);
    }

    /**
     * @return ArrayCollection<PostStartWaiter>
     */
    public function getPostStartWaiters(): ArrayCollection
    {
        return new ArrayCollection($this->postStartWaiters);
    }

    /**
     * @return ArrayCollection<PostStartCommand>
     */
    public function getPostStartCommands(): ArrayCollection
    {
        return new ArrayCollection($this->postStartCommands);
    }

    /**
     * @return ArrayCollection<PostDestroyCommand>
     */
    public function getPostDestroyCommands(): ArrayCollection
    {
        return new ArrayCollection($this->postDestroyCommands);
    }

    /** @internal */
    public function setPreStartCommands(array $preStartCommands): void
    {
        $this->preStartCommands = $preStartCommands;
    }

    /** @internal */
    public function setPostStartWaiters(array $postStartWaiters): void
    {
        $this->postStartWaiters = $postStartWaiters;
    }

    /** @internal */
    public function setPostStartCommands(array $postStartCommands): void
    {
        $this->postStartCommands = $postStartCommands;
    }

    /** @internal */
    public function setPostDestroyCommands(array $postDestroyCommands): void
    {
        $this->postDestroyCommands = $postDestroyCommands;
    }
}
