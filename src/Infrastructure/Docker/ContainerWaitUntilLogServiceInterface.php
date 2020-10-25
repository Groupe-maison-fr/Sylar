<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;

interface ContainerWaitUntilLogServiceInterface
{
    public function wait(ContainerParameterDTO $containerParameter, string $expression, int $timeout);
}
