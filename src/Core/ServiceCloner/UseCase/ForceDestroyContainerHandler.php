<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Docker\ContainerDeleteServiceInterface;
use App\Infrastructure\Docker\ContainerStopServiceInterface;
use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ForceDestroyContainerHandler implements MessageHandlerInterface
{
    private ContainerStopServiceInterface $containerStopService;
    private ServerSideEventPublisherInterface $serverSideEventPublisher;
    private ContainerDeleteServiceInterface $containerDeleteService;

    public function __construct(
        ContainerStopServiceInterface $containerStopService,
        ContainerDeleteServiceInterface $containerDeleteService,
        ServerSideEventPublisherInterface $serverSideEventPublisher
    ) {
        $this->containerStopService = $containerStopService;
        $this->containerDeleteService = $containerDeleteService;
        $this->serverSideEventPublisher = $serverSideEventPublisher;
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
