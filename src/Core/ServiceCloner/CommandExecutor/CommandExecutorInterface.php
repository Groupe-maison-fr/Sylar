<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\CommandExecutor;

use App\Core\ServiceCloner\Configuration\Object\Command;

interface CommandExecutorInterface
{
    /**
     * @return CommandExecutorResultDTO[]
     */
    public function run(Command $command): array;
}
