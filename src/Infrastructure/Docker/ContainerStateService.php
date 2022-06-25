<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\Port;

final class ContainerStateService implements ContainerStateServiceInterface
{
    private ContainerFinderService $dockerFinderService;

    public function __construct(
        ContainerFinderService $dockerFinderService
    ) {
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
