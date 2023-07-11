<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class PostStartCommand
{
    private string $executionEnvironment;

    /**
     * @var string[]
     */
    private array $command = [];

    public function __construct()
    {
    }

    public function getExecutionEnvironment(): string
    {
        return $this->executionEnvironment;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getCommand(): ArrayCollection
    {
        return new ArrayCollection($this->command);
    }

    /** @internal */
    public function setExecutionEnvironment(string $executionEnvironment): void
    {
        $this->executionEnvironment = $executionEnvironment;
    }

    /**
     * @internal
     *
     * @param string[] $command
     */
    public function setCommand(array $command): void
    {
        $this->command = $command;
    }
}
