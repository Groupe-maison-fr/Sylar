<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Docker\ContainerDeleteServiceInterface;
use App\Infrastructure\Docker\ContainerStopServiceInterface;
use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ForceDestroyContainerHandler
{
    public function __construct(
        private ContainerStopServiceInterface $containerStopService,
        private ContainerDeleteServiceInterface $containerDeleteService,
        private ServerSideEventPublisherInterface $serverSideEventPublisher,
    ) {
    }

    public function __invoke(ForceDestroyContainerCommand $forceDestroyContainerCommand): void
    {
        try {
            $this->containerStopService->stop($forceDestroyContainerCommand->getName());
            $this->containerDeleteService->delete($forceDestroyContainerCommand->getName());
            $this->serverSideEventPublisher->publish('sylar', [
                'type' => 'container',
                'action' => 'destroy',
                'data' => [
                    'name' => $forceDestroyContainerCommand->getName(),
                ],
            ]);
        } catch (Exception $exception) {
            $this->serverSideEventPublisher->publish('sylar', [
                'type' => 'container',
                'action' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                    'name' => $forceDestroyContainerCommand->getName(),
                ],
            ]);
        }
    }
}
