<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class StartServiceHandler implements MessageHandlerInterface
{
    private ServiceClonerServiceInterface $serviceClonerService;

    public function __construct(
        ServiceClonerServiceInterface $serviceClonerService
    ) {
        $this->serviceClonerService = $serviceClonerService;
    }

    public function __invoke(StartServiceCommand $startServiceCommand): void
    {
        $this->serviceClonerService->startService(
            $startServiceCommand->getMasterName(),
            $startServiceCommand->getInstanceName(),
            $startServiceCommand->getIndex() === null ? null : (int) $startServiceCommand->getIndex()
        );
    }
}
