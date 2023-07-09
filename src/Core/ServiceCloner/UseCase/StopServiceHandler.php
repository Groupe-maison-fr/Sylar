<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class StopServiceHandler
{
    public function __construct(
        private ServiceClonerServiceInterface $serviceClonerService,
    ) {
    }

    public function __invoke(StopServiceCommand $stopServiceCommand): void
    {
        $this->serviceClonerService->stop(
            $stopServiceCommand->getMasterName(),
            $stopServiceCommand->getInstanceName(),
        );
    }
}
