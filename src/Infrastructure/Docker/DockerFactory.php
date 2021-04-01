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

    public function __construct(
        DockerApiLogger $dockerApiLogger
    ) {
        $this->dockerApiLogger = $dockerApiLogger;
    }

    public function create(): Docker
    {
        /* @phpstan-ignore-next-line */
        return Docker::create(new PluginClient(DockerClientFactory::createFromEnv(), [
            new LoggerPlugin($this->dockerApiLogger),
        ]));
    }
}
