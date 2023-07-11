<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Mutation;

use DomainException;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final class MutationRetryMessage implements MutationInterface
{
    /** @param ReceiverInterface&ListableReceiverInterface $receiver */
    public function __construct(
        private ReceiverInterface $receiver,
        private KernelInterface $kernel,
    ) {
    }

    /**
     * @return array{
     *     success: bool
     * }
     */
    public function __invoke(string $messageId): array
    {
        $envelop = $this->receiver->find($messageId);
        if ($envelop === null) {
            throw new DomainException(sprintf('Message not found with id "%s"', $messageId));
        }
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'messenger:failed:retry',
            'id' => [$messageId],
            '--force' => true,
        ]);

        $output = new BufferedOutput();
        $exitCode = $application->run($input, $output);

        return ['success' => $exitCode === 0];
    }
}
