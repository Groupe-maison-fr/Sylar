<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;

/**
 * @internal
 */
final class ServiceClonerStateServiceIntegrationTest extends AbstractServiceCloneServiceIntegrationTestCase
{
    /**
     * @test
     */
    public function it_should_get_service_states(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'network');
        $serviceClonerStateService = $this->getService(ServiceClonerStateServiceInterface::class);
        $this->serviceCloneService->startMaster('unit-test-go-static-webserver');
        $this->serviceCloneService->startService('unit-test-go-static-webserver', 'instance_01', 1);
        $this->serviceCloneService->startService('unit-test-go-static-webserver', 'instance_02', 2);
        self::assertSame(
            [
                'unit-test-go-static-webserver',
                'unit-test-go-static-webserver_instance-01',
                'unit-test-go-static-webserver_instance-02',
            ],
            array_values(array_filter(
                array_map(fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getContainerName(), $serviceClonerStateService->getStates()),
                fn (string $containerName) => preg_match('!^unit-test!', $containerName),
            )),
        );
    }
}
