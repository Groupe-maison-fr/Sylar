<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\Docker;
use Exception;
use Psr\Log\LoggerInterface;

final class ContainerDeleteService implements ContainerDeleteServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
    }

    public function delete(
        string $containerName
    ): void {
        try {
            $this->docker->containerDelete($containerName);
        } catch (Exception $exception) {
            $this->logger->error(sprintf('DeleteDocker: %s', $exception->getMessage()));
            throw $exception;
        }
    }
}
