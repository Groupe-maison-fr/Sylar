<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\RestartServiceCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\RestartServiceSuccessOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class RestartServiceMutation implements MutationInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(string $masterName, string $instanceName, ?int $index): RestartServiceSuccessOutputDTO|FailedOutputDTO
    {
        try {
            $this->messageBus->dispatch(new RestartServiceCommand(
                $masterName,
                $instanceName,
                $index === null ? null : (int) $index,
            ));

            return new RestartServiceSuccessOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
