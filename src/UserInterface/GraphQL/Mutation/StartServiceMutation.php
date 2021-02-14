<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\StartServiceCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\StartServiceSuccessOutputDTO;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class StartServiceMutation implements MutationInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $masterName, string $instanceName, ?int $index)
    {
        try {
            $this->messageBus->dispatch(new StartServiceCommand(
                $masterName,
                $instanceName,
                $index === null ? null : (int) $index
            ));

            return new StartServiceSuccessOutputDTO(true);
        } catch (\Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
