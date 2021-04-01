<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

use Doctrine\Common\Collections\ArrayCollection;

final class Command
{
    private string $name;

    /**
     * @var string[] | ArrayCollection
     */
    private ArrayCollection $subCommands;

    public function __construct()
    {
        $this->subCommands = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function addSubCommand(string $command): void
    {
        $this->subCommands->add($command);
    }

    public function removeSubCommand(string $command): void
    {
        $this->subCommands->remove($command);
    }

    /**
     * @return string[] | ArrayCollection
     */
    public function getSubCommands(): ArrayCollection
    {
        return $this->subCommands;
    }
}
