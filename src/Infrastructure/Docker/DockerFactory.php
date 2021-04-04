<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\Docker;
use Docker\DockerClientFactory;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;

final class DockerFactory implements DockerFactoryInterface
{
    private DockerApiLogger $dockerApiLogger;
    private string $dockerRemoteSocket;

    public function __construct(
        DockerApiLogger $dockerApiLogger,
        string $dockerRemoteSocket
    ) {
        $this->dockerApiLogger = $dockerApiLogger;
        $this->dockerRemoteSocket = $dockerRemoteSocket;
    }

    public function create(): Docker
    {
        $httpClient = new PluginClient(DockerClientFactory::create([
            'remote_socket' => $this->dockerRemoteSocket,
        ]), [
            new LoggerPlugin($this->dockerApiLogger),
        ]);
        /* @phpstan-ignore-next-line */
        return Docker::create($httpClient);
    }
}
