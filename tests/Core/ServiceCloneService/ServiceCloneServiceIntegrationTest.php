<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use Docker\API\Model\ContainerSummaryItem;
use DomainException;

/**
 * @internal
 */
final class ServiceCloneServiceIntegrationTest extends AbstractServiceCloneServiceIntegrationTest
{
    /**
     * @test
     */
    public function it_should_load_configuration_service_with_specific_path(): void
    {
        $this->setConfigurationDependentServices('specific_configuration');
        $config = $this->configurationService->getConfiguration()->getServiceByName('mysql_specific_configuration');
        self::assertSame('ABCDEFGH', $config->getLabels()->get(1)->getValue());
    }

    /**
     * @test
     */
    public function it_should_load_configuration_service_with_commands(): void
    {
        $this->setConfigurationDependentServices('commands');
        self::assertCount(2, $this->configurationService->getConfiguration()->getCommands()->toArray());
        self::assertCount(2, $this->configurationService->getConfiguration()->getCommandByName('test1')->getSubCommands()->toArray());
        self::assertCount(3, $this->configurationService->getConfiguration()->getCommandByName('test2')->getSubCommands()->toArray());
        self::assertSame('pwd', $this->configurationService->getConfiguration()->getCommandByName('test2')->getSubCommands()->toArray()[0]);
        self::assertNull($this->configurationService->getConfiguration()->getCommandByName('test3'));
    }

    /**
     * @test
     */
    public function it_should_load_configuration_service_with_network(): void
    {
        $this->setConfigurationDependentServices('network');
        $this->serviceCloneService->startMaster('go-static-webserver');
        $this->serviceCloneService->startService('go-static-webserver', '02', 2);
        $dockerInspectionMaster = json_decode($this->process->run('docker', 'inspect', 'go-static-webserver')->getStdOutput(), true);
        $dockerInspectionService = json_decode($this->process->run('docker', 'inspect', 'go-static-webserver_02')->getStdOutput(), true);
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
        $this->setConfigurationDependentServices('specific_configuration');
        $config = $this->configurationService->getConfiguration()->getServiceByName('not_existing_service_name');
        self::assertNull($config);
    }

    /**
     * @test
     */
    public function it_should_create_a_container_and_run_lifecycles_hooks(): void
    {
        $this->setConfigurationDependentServices('lifecycle_hooks');
        $this->resetBufferedLoggerHandler();

        $dockerName = $this->configurationService->getConfiguration()->getServices()[0]->getName();
        $this->serviceCloneService->startMaster($dockerName);

        /** @var ContainerSummaryItem $containerSummaryItem */
        $containerSummaryItem = $this->containerFinderService->getDockerByName($dockerName);

        self::assertSame('running', $containerSummaryItem->getState());
        $this->serviceCloneService->stop($dockerName, 'master');
        $this->assertContainsLogThatMatchRegularExpression('!Listening at 0\.0\.0\.0!');
        $this->assertContainsLogWithSameMessage('Process launched "sudo docker ps --no-trunc"');
        $this->assertContainsLogThatMatchRegularExpression('!Listening at 0\.0\.0\.0!');
        $this->assertContainsLogWithSameMessage('Process launched "sudo ls -lah /"');
        $this->assertContainsLogWithSameMessage('Process launched "sudo ls -ahl /"');
    }

    /**
     * @test
     */
    public function it_should_start_master_and_clones_and_can_not_stop_master_if_clone_is_running(): void
    {
        $this->setConfigurationDependentServices('network');
        $this->serviceCloneService->startMaster('go-static-webserver');
        $this->serviceCloneService->startService('go-static-webserver', 'instance_01', 1);
        $this->serviceCloneService->startService('go-static-webserver', 'instance_02', 2);

        self::expectException(DomainException::class);
        self::expectExceptionMessage('Can not delete "go-static-webserver", some dependant services are still there [instance_01,instance_02]');
        $this->serviceCloneService->stop('go-static-webserver', 'master');
    }
}
