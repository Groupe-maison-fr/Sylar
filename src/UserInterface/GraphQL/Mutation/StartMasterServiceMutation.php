<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\UserInterface\Cli\StartMasterServiceCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\StartServiceSuccessOutputDTO;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class StartMasterServiceMutation implements MutationInterface
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
            $this->messageBus->dispatch(new StartMasterServiceCommand(
                $masterName
            ));

            return new StartServiceSuccessOutputDTO(true);
        } catch (\Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
