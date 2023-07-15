<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\Reservation\ReservationRepositoryInterface;
use App\UserInterface\GraphQL\Map\DeleteReservationOutputDTO;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

final class DeleteReservationMutation implements MutationInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
    ) {
    }

    public function __invoke(string $service, string $name, int $index): DeleteReservationOutputDTO|FailedOutputDTO
    {
        try {
            $this->reservationRepository->delete($service, $name, $index);

            return new DeleteReservationOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
