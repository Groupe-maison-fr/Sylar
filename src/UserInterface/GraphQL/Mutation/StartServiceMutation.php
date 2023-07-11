<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\StartServiceCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\StartServiceSuccessOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class StartServiceMutation implements MutationInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(string $masterName, string $instanceName, ?int $index): StartServiceSuccessOutputDTO|FailedOutputDTO
    {
        try {
            $this->messageBus->dispatch(new StartServiceCommand(
                $masterName,
                $instanceName,
                $index === null ? null : (int) $index,
            ));

            return new StartServiceSuccessOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
