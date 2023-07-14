<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\Reservation\Object\Reservation;
use App\Core\ServiceCloner\Reservation\ReservationRepositoryInterface;
use App\UserInterface\GraphQL\Map\AddReservationOutputDTO;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

final class AddReservationMutation implements MutationInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
    ) {
    }

    public function __invoke(string $service, string $name, int $index): AddReservationOutputDTO|FailedOutputDTO
    {
        try {
            $this->reservationRepository->add(new Reservation($service, $name, $index));

            return new AddReservationOutputDTO(true);
        } catch (Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
