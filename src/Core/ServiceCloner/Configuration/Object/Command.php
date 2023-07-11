<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class Command
{
    private string $name;

    /**
     * @var string[]
     */
    private array $subCommands = [];

    public function __construct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addSubCommand(string $command): void
    {
        $this->subCommands[] = $command;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getSubCommands(): ArrayCollection
    {
        return new ArrayCollection($this->subCommands);
    }

    /** @internal */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @internal
     *
     * @param string[] $subCommands
     */
    public function setSubCommands(array $subCommands): void
    {
        $this->subCommands = $subCommands;
    }
}
