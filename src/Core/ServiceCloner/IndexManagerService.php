<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

final class IndexManagerService implements IndexManagerServiceInterface
{
    private ServiceClonerStateServiceInterface $serviceClonerStateService;

    public function __construct(
        ServiceClonerStateServiceInterface $serviceClonerStateService
    ) {
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function getNextAvailable(): int
    {
        $nextAvailableIndex = 1;
        $stateIndexes = array_map(fn ($state) => $state->getIndex(), $this->serviceClonerStateService->getStates());
        sort($stateIndexes, SORT_NUMERIC);
        foreach ($stateIndexes as $index) {
            if ($index === $nextAvailableIndex) {
                ++$nextAvailableIndex;
            }
        }

        return $nextAvailableIndex;
    }
}
