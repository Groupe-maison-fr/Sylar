<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\StopServiceCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\StopServiceSuccessOutputDTO;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class StopServiceMutation implements MutationInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(string $masterName, string $instanceName)
    {
        try {
            $this->messageBus->dispatch(new StopServiceCommand(
                $masterName,
                $instanceName
            ));

            return new StopServiceSuccessOutputDTO(true);
        } catch (\Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
