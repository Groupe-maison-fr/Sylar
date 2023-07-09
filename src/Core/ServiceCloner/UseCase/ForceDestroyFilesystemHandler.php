<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ForceDestroyFilesystemHandler
{
    public function __construct(
        private FilesystemServiceInterface $filesystemService,
        private ServerSideEventPublisherInterface $serverSideEventPublisher,
    ) {
    }

    public function __invoke(ForceDestroyFilesystemCommand $forceDestroyFilesystemCommand): void
    {
        try {
            $this->filesystemService->destroyFilesystem($forceDestroyFilesystemCommand->getName(), true);
            $this->serverSideEventPublisher->publish('sylar', [
                'type' => 'filesystem',
                'action' => 'destroy',
                'data' => [
                    'name' => $forceDestroyFilesystemCommand->getName(),
                ],
            ]);
        } catch (Exception $exception) {
            $this->serverSideEventPublisher->publish('sylar', [
                'type' => 'filesystem',
                'action' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                ],
            ]);
        }
    }
}
