<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\KernelEvent;

use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

final class BusWorkerSubscriber implements EventSubscriberInterface
{
    private ServerSideEventPublisherInterface $serverSideEventPublisher;

    public function __construct(
        ServerSideEventPublisherInterface $serverSideEventPublisher,
    ) {
        $this->serverSideEventPublisher = $serverSideEventPublisher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
        ];
    }

    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event): void
    {
        if ($event->willRetry()) {
            return;
        }

        $envelope = $event->getEnvelope();
        if ($envelope->last(SentToFailureTransportStamp::class) !== null) {
            return;
        }

        /** @var ErrorDetailsStamp $errorDetailStamp */
        $errorDetailStamp = $envelope->last(ErrorDetailsStamp::class);
        $this->serverSideEventPublisher->publish('sylar', [
            'type' => 'failedMessage',
            'action' => 'new',
            'data' => [
                'message' => $errorDetailStamp->getExceptionMessage(),
                'exception' => $errorDetailStamp->getExceptionClass(),
            ],
        ]);
    }
}
