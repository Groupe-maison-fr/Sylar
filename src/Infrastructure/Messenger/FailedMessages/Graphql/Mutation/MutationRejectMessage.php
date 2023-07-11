<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Mutation;

use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final class MutationRejectMessage implements MutationInterface
{
    /** @param ReceiverInterface&ListableReceiverInterface $receiver */
    public function __construct(
        private ReceiverInterface $receiver,
    ) {
    }

    /**
     * @param mixed[] $messageIds
     */
    public function __invoke(
        array $messageIds,
    ): bool {
        array_map(function (string $messageId): void {
            $envelop = $this->receiver->find($messageId);
            $this->receiver->reject($envelop);
        }, $messageIds);

        return true;
    }
}
