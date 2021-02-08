<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\Docker;
use Psr\Log\LoggerInterface;

final class ContainerStopService implements ContainerStopServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;
    private ContainerFinderServiceInterface $dockerFinderService;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger,
        ContainerFinderServiceInterface $dockerFinderService
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
        $this->dockerFinderService = $dockerFinderService;
    }

    public function stop(string $dockerName, string ...$arguments): void
    {
        $container = $this->dockerFinderService->getDockerByName($dockerName);
        if ($container === null) {
            return;
        }

        $this->docker->containerStop($container->getId());
    }
}
