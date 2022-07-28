<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Repository;

use App\Infrastructure\Messenger\FailedMessages\FailedMessageDTO;
use App\Infrastructure\Messenger\FailedMessages\FailedMessageDTOFactoryInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Traversable;

final class FailedMessagesRepository implements FailedMessagesRepositoryInterface
{
    private ListableReceiverInterface $receiver;
    private FailedMessageDTOFactoryInterface $failedMessageDTOFactory;

    public function __construct(
        ListableReceiverInterface $receiver,
        FailedMessageDTOFactoryInterface $failedMessageDTOFactory
    ) {
        $this->receiver = $receiver;
        $this->failedMessageDTOFactory = $failedMessageDTOFactory;
    }

    public function findAll(int $max): array
    {
        $envelopes = $this->receiver->all($max);

        if ($envelopes instanceof Traversable) {
            $envelopes = iterator_to_array($envelopes);
        }

        return array_map(fn (Envelope $envelope) => $this->failedMessageDTOFactory->create($envelope), $envelopes);
    }

    public function getById(int $id): FailedMessageDTO
    {
        $envelope = $this->receiver->find($id);
        if ($envelope === null) {
            throw new RuntimeException(sprintf('The message "%s" was not found.', $id));
        }

        return $this->failedMessageDTOFactory->create($envelope);
    }
}
