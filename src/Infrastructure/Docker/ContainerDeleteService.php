<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\Docker;
use Exception;
use Psr\Log\LoggerInterface;

final class ContainerDeleteService implements ContainerDeleteServiceInterface
{
    public function __construct(
        private Docker $dockerReadWrite,
        private LoggerInterface $logger,
    ) {
    }

    public function delete(
        string $containerName,
    ): void {
        try {
            $this->dockerReadWrite->containerDelete($containerName);
        } catch (Exception $exception) {
            $this->logger->error(sprintf('DeleteDocker: %s', $exception->getMessage()));
            throw $exception;
        }
    }
}
