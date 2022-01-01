<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;

/**
 * @internal
 */
final class ServiceClonerStateServiceIntegrationTest extends AbstractServiceCloneServiceIntegrationTest
{
    /**
     * @test
     */
    public function it_should_get_service_states(): void
    {
        $this->setConfigurationDependentServices('network');
        $serviceClonerStateService = $this->getService(ServiceClonerStateServiceInterface::class);
        $this->serviceCloneService->startMaster('go-static-webserver');
        $this->serviceCloneService->startService('go-static-webserver', 'instance_01', 1);
        $this->serviceCloneService->startService('go-static-webserver', 'instance_02', 2);
        self::assertSame(
            [
                'go-static-webserver',
                'go-static-webserver_instance-01',
                'go-static-webserver_instance-02',
            ],
            array_map(fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getContainerName(), $serviceClonerStateService->getStates())
        );
    }
}
