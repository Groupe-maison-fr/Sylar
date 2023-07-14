<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Reservation\Object\Reservation;
use App\Core\ServiceCloner\Reservation\ReservationRepositoryInterface;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class ReservationResolver implements QueryInterface
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
    ) {
    }

    public function __invoke(ResolveInfo $info, Reservation $reservation, Argument $args): mixed
    {
        switch ($info->fieldName) {
            case 'name':
                return $reservation->getName();
            case 'service':
                return $reservation->getService();
            case 'index':
                return $reservation->getIndex();
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    /**
     * @return Reservation[]
     */
    public function resolve(): array
    {
        return $this->reservationRepository->findAll();
    }
}
