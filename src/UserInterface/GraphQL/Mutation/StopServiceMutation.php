<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\StopServiceCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\StopServiceSuccessOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class StopServiceMutation implements MutationInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(string $masterName, string $instanceName): StopServiceSuccessOutputDTO|FailedOutputDTO
    {
        try {
            $this->messageBus->dispatch(new StopServiceCommand(
                $masterName,
                $instanceName,
            ));

            return new StopServiceSuccessOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
