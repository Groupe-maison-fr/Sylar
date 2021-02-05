<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\Docker;

interface DockerFactoryInterface
{
    public function create(): Docker;
}
