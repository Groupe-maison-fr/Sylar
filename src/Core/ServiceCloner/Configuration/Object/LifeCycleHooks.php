<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class LifeCycleHooks
{
    /**
     * @var PreStartCommand[]|ArrayCollection
     */
    private $preStartCommands;

    /**
     * @var PostStartWaiter[]|ArrayCollection
     */
    private $postStartWaiters;

    /**
     * @var PostStartCommand[]|ArrayCollection
     */
    private $postStartCommands;

    /**
     * @var PostDestroyCommand[]|ArrayCollection
     */
    private $postDestroyCommands;

    public function __construct()
    {
        $this->preStartCommands = new ArrayCollection();
        $this->postStartWaiters = new ArrayCollection();
        $this->postStartCommands = new ArrayCollection();
        $this->postDestroyCommands = new ArrayCollection();
    }

    public function addPreStartCommand(PreStartCommand $preStartCommand): void
    {
        $this->preStartCommands[] = $preStartCommand;
    }

    /**
     * @return PreStartCommand[]|ArrayCollection
     */
    public function getPreStartCommands(): ArrayCollection
    {
        return $this->preStartCommands;
    }

    public function removePreStartCommand(PreStartCommand $preStartCommand): void
    {
        $this->preStartCommands->removeElement($preStartCommand);
    }

    public function addPostStartWaiter(PostStartWaiter $postStartWaiter): void
    {
        $this->postStartWaiters[] = $postStartWaiter;
    }

    /**
     * @return PostStartWaiter[]|ArrayCollection
     */
    public function getPostStartWaiters(): ArrayCollection
    {
        return $this->postStartWaiters;
    }

    public function removePostStartWaiter(PostStartWaiter $postStartWaiter): void
    {
        $this->postStartWaiters->removeElement($postStartWaiter);
    }

    public function addPostStartCommand(PostStartCommand $postStartCommand): void
    {
        $this->postStartCommands[] = $postStartCommand;
    }

    /**
     * @return PostStartCommand[]|ArrayCollection
     */
    public function getPostStartCommands(): ArrayCollection
    {
        return $this->postStartCommands;
    }

    public function removePostStartCommand(PostStartCommand $postStartCommand): void
    {
        $this->postStartCommands->removeElement($postStartCommand);
    }

    public function addPostDestroyCommand(PostDestroyCommand $postDestroyCommand): void
    {
        $this->postDestroyCommands[] = $postDestroyCommand;
    }

    /**
     * @return PostDestroyCommand[]|ArrayCollection
     */
    public function getPostDestroyCommands(): ArrayCollection
    {
        return $this->postDestroyCommands;
    }

    public function removePostDestroyCommand(PostDestroyCommand $postDestroyCommand): void
    {
        $this->postDestroyCommands->removeElement($postDestroyCommand);
    }
}
