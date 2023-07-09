<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\API\Client;

interface DockerFactoryInterface
{
    public function create(string $dockerRemoteSocket = null): Client;
}
