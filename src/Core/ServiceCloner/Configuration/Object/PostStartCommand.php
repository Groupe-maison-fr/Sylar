<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class PostStartCommand
{
    private string $executionEnvironment;

    /**
     * @var string[] | ArrayCollection
     */
    private $command;

    public function __construct()
    {
        $this->command = new ArrayCollection();
    }

    public function getExecutionEnvironment(): string
    {
        return $this->executionEnvironment;
    }

    public function setExecutionEnvironment(string $executionEnvironment): void
    {
        $this->executionEnvironment = $executionEnvironment;
    }

    public function addCommand(string $command): void
    {
        $this->command[] = $command;
    }

    /**
     * @return string[] | ArrayCollection
     */
    public function getCommand(): ArrayCollection
    {
        return $this->command;
    }

    public function removeCommand(string $command): void
    {
        $this->command->removeElement($command);
    }
}
