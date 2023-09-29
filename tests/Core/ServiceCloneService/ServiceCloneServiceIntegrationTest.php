<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Core\ServiceCloner\ServiceClonerNamingServiceInterface;
use Docker\API\Model\ContainerSummaryItem;
use DomainException;

/**
 * @internal
 */
final class ServiceCloneServiceIntegrationTest extends AbstractServiceCloneServiceIntegrationTestCase
{
    /**
     * @test
     */
    public function it_should_load_configuration_service_with_specific_path(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'specific_configuration');
        $config = $this->configurationService->getConfiguration()->getServiceByName('unit-test-mysql_specific_configuration');
        self::assertSame('ABCDEFGH', $config->labels[1]->value);
    }

    /**
     * @test
     */
    public function it_should_load_configuration_service_with_commands(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'commands');
        self::assertCount(2, $this->configurationService->getConfiguration()->commands);
        self::assertCount(2, $this->configurationService->getConfiguration()->getCommandByName('test1')->subCommands);
        self::assertCount(3, $this->configurationService->getConfiguration()->getCommandByName('test2')->subCommands);
        self::assertSame('pwd', $this->configurationService->getConfiguration()->getCommandByName('test2')->subCommands[0]);
        self::assertNull($this->configurationService->getConfiguration()->getCommandByName('test3'));
    }

    /**
     * @test
     */
    public function it_should_load_configuration_service_with_network(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'network');
        $this->serviceCloneService->startMaster('unit-test-go-static-webserver');
        $this->serviceCloneService->startService('unit-test-go-static-webserver', '02', 2);
        $dockerInspectionMaster = json_decode($this->process->run('docker', 'inspect', 'unit-test-go-static-webserver')->getStdOutput(), true);
        $dockerInspectionService = json_decode($this->process->run('docker', 'inspect', 'unit-test-go-static-webserver_02')->getStdOutput(), true);
        self::assertArrayHasKey('n1', $dockerInspectionMaster[0]['NetworkSettings']['Networks']);
        self::assertArrayNotHasKey('none', $dockerInspectionMaster[0]['NetworkSettings']['Networks']);
        self::assertArrayNotHasKey('n1', $dockerInspectionService[0]['NetworkSettings']['Networks']);
        self::assertArrayHasKey('none', $dockerInspectionService[0]['NetworkSettings']['Networks']);
    }

    /**
     * @test
     */
    public function it_should_not_retrieve_configuration_for_non_existing_service(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'specific_configuration');
        $config = $this->configurationService->getConfiguration()->getServiceByName('not_existing_service_name');
        self::assertNull($config);
    }

    /**
     * @test
     */
    public function it_should_create_a_container_and_run_lifecycles_hooks(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'lifecycle_hooks');
        $this->resetBufferedLoggerHandler();

        $dockerName = $this->configurationService->getConfiguration()->services[0]->name;
        $this->serviceCloneService->startMaster($dockerName);

        /** @var ContainerSummaryItem $containerSummaryItem */
        $containerSummaryItem = $this->containerFinderService->getDockerByName($dockerName);

        sleep(4);

        self::assertSame('running', $containerSummaryItem->getState());
        $this->assertContainsLogThatMatchRegularExpression('!start worker processes!');
        $this->assertContainsLogThatMatchRegularExpression('!Process launched ".*docker ps --no-trunc"!');
        $this->assertContainsLogThatMatchRegularExpression('!start worker processes!');
        $this->assertContainsLogThatMatchRegularExpression('!Process launched ".*ls -lah /"!');
        $this->serviceCloneService->stop($dockerName, ServiceClonerNamingServiceInterface::MASTER_NAME);
    }

    /**
     * @test
     */
    public function it_should_create_a_container_and_run_lifecycles_hooks_(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'when_disabling_all_lifecycle_hooks');
        $this->resetBufferedLoggerHandler();

        $dockerName = $this->configurationService->getConfiguration()->services[0]->name;
        $this->serviceCloneService->startMaster($dockerName);

        /** @var ContainerSummaryItem $containerSummaryItem */
        $containerSummaryItem = $this->containerFinderService->getDockerByName($dockerName);

        sleep(4);

        self::assertSame('running', $containerSummaryItem->getState());
        $this->assertNotContainsLogThatMatchRegularExpression('!should not appear!');
        $this->serviceCloneService->stop($dockerName, ServiceClonerNamingServiceInterface::MASTER_NAME);
    }

    /**
     * @test
     */
    public function it_should_start_master_and_clones_and_can_not_stop_master_if_clone_is_running(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'network');
        $this->serviceCloneService->startMaster('unit-test-go-static-webserver');
        $this->serviceCloneService->startService('unit-test-go-static-webserver', 'instance_01', 1);
        $this->serviceCloneService->startService('unit-test-go-static-webserver', 'instance_02', 2);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Can not delete "unit-test-go-static-webserver", some dependant services are still there [instance_01,instance_02]');
        $this->serviceCloneService->stop('unit-test-go-static-webserver', ServiceClonerNamingServiceInterface::MASTER_NAME);
    }
}
