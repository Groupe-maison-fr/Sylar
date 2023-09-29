<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\API\Client;
use Docker\Docker;
use Docker\DockerClientFactory;
use Http\Client\Common\Plugin\LoggerPlugin;

final class DockerFactory implements DockerFactoryInterface
{
    public function __construct(
        private DockerApiLogger $dockerApiLogger,
    ) {
    }

    public function create(string $dockerRemoteSocket = null): Client
    {
        return Docker::create(DockerClientFactory::createFromEnv(
            null,
            [
                'remote_socket' => $dockerRemoteSocket,
            ],
            [
                new LoggerPlugin($this->dockerApiLogger),
            ],
        ));
    }
}
