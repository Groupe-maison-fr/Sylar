<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

final class IndexManagerService implements IndexManagerServiceInterface
{
    public function __construct(
        private ServiceClonerStateServiceInterface $serviceClonerStateService,
    ) {
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
