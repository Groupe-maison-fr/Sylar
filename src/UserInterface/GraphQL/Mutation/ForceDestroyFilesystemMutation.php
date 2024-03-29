<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\UseCase\ForceDestroyFilesystemCommand;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\ForceDestroyFilesystemOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ForceDestroyFilesystemMutation implements MutationInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(string $name): ForceDestroyFilesystemOutputDTO|FailedOutputDTO
    {
        try {
            $this->messageBus->dispatch(new ForceDestroyFilesystemCommand($name));

            return new ForceDestroyFilesystemOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
