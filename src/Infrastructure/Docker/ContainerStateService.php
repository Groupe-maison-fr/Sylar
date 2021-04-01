<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\Port;
use Docker\Docker;
use Psr\Log\LoggerInterface;

final class ContainerStateService implements ContainerStateServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;
    private ContainerFinderService $dockerFinderService;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger,
        ContainerFinderService $dockerFinderService
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
        $this->dockerFinderService = $dockerFinderService;
    }

    public function dockerState(string $dockerName): ?string
    {
        $container = $this->dockerFinderService->getDockerByName($dockerName);

        return $container ? $container->getState() : null;
    }

    public function dockerExposedPorts(string $dockerName): ?array
    {
        $container = $this->dockerFinderService->getDockerByName($dockerName);

        return $container ? array_map(fn (Port $port) => $port->getPublicPort(), $container->getPorts()) : [];
    }
}
