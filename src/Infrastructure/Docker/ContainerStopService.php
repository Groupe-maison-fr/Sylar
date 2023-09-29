<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\Docker;

final class ContainerStopService implements ContainerStopServiceInterface
{
    public function __construct(
        private Docker $dockerReadWrite,
        private ContainerFinderServiceInterface $dockerFinderService,
    ) {
    }

    public function stop(string $dockerName, string ...$arguments): void
    {
        $container = $this->dockerFinderService->getDockerByName($dockerName);
        if ($container === null) {
            return;
        }

        $this->dockerReadWrite->containerStop($container->getId());
    }
}
