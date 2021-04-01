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
        foreach ($this->serviceClonerStateService->getStates() as $state) {
            if ($state->getIndex() === $nextAvailableIndex) {
                ++$nextAvailableIndex;
            }
        }

        return $nextAvailableIndex;
    }
}
