<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Reservation\ReservationRepositoryInterface;

final class IndexManagerService implements IndexManagerServiceInterface
{
    public function __construct(
        private ServiceClonerStateServiceInterface $serviceClonerStateService,
        private ReservationRepositoryInterface $serviceReservationRepository,
    ) {
    }

    public function getNextAvailable(string $serviceName): int
    {
        $nextAvailableIndex = 1;
        $stateIndexes = array_values(array_map(fn ($state) => $state->getIndex(), $this->serviceClonerStateService->getStatesByService($serviceName)));
        $reservedIndexes = $this->serviceReservationRepository->getReservationIndexesByService($serviceName);
        sort($stateIndexes, SORT_NUMERIC);
        while (in_array($nextAvailableIndex, $stateIndexes, true) || in_array($nextAvailableIndex, $reservedIndexes, true)) {
            ++$nextAvailableIndex;
        }

        return $nextAvailableIndex;
    }
}
