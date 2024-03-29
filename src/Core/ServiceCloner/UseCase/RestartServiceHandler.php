<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RestartServiceHandler
{
    public function __construct(
        private ServiceClonerServiceInterface $serviceClonerService,
    ) {
    }

    public function __invoke(RestartServiceCommand $startServiceCommand): void
    {
        $this->serviceClonerService->restartService(
            $startServiceCommand->getMasterName(),
            $startServiceCommand->getInstanceName(),
            $startServiceCommand->getIndex() === null ? null : (int) $startServiceCommand->getIndex(),
        );
    }
}
