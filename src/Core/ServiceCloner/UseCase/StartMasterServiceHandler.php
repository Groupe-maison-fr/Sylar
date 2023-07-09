<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class StartMasterServiceHandler
{
    private ServiceClonerServiceInterface $serviceClonerService;

    public function __construct(
        ServiceClonerServiceInterface $serviceClonerService,
    ) {
        $this->serviceClonerService = $serviceClonerService;
    }

    public function __invoke(StartMasterServiceCommand $startMasterServiceCommand): void
    {
        $this->serviceClonerService->startMaster(
            $startMasterServiceCommand->getMasterName(),
        );
    }
}
