<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\ForceDestroyContainerCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\ForceDestroyContainerOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ForceDestroyContainerMutation implements MutationInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(string $name): ForceDestroyContainerOutputDTO|FailedOutputDTO
    {
        try {
            $this->messageBus->dispatch(new ForceDestroyContainerCommand($name));

            return new ForceDestroyContainerOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
