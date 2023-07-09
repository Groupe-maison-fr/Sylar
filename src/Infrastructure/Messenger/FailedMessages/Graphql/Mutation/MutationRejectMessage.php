<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Mutation;

use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final class MutationRejectMessage implements MutationInterface
{
    /** @var ReceiverInterface&ListableReceiverInterface */
    private $receiver;

    /** @param ReceiverInterface&ListableReceiverInterface $receiver */
    public function __construct(ReceiverInterface $receiver)
    {
        $this->receiver = $receiver;
    }

    public function __invoke(
        array $messageIds,
    ) {
        array_map(function (string $messageId): void {
            $envelop = $this->receiver->find($messageId);
            $this->receiver->reject($envelop);
        }, $messageIds);

        return true;
    }
}
