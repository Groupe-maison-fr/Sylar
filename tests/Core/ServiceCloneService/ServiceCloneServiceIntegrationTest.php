<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Common\Tests\LoggerAwareTestTrait;
use App\Core\ServiceCloner\Configuration\ConfigurationService;
use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerExecServiceInterface;
use App\Infrastructure\Docker\ContainerFinderServiceInterface;
use App\Infrastructure\Docker\ContainerStopServiceInterface;
use App\Infrastructure\Process\SudoProcess;
use Docker\API\Model\ContainerSummaryItem;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class ServiceCloneServiceIntegrationTest extends AbstractIntegrationTest
{
    use LoggerAwareTestTrait;

    private ServiceClonerServiceInterface $serviceCloneService;
    private ContainerExecServiceInterface $containerExecService;
    private ContainerCreationServiceInterface $containerCreationService;
    private ConfigurationServiceInterface $configurationService;
    private string $testRoot;
    private SudoProcess $process;

    private ContainerFinderServiceInterface $containerFinderService;

    private ContainerStopServiceInterface $containerStopService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initLoggerBufferedHandler($this->getService('logger'));

        $this->process = $this->getService(SudoProcess::class);
        $this->testRoot = '/tmp';
        $this->cleanExistingDockers();

        echo $this->process->mayRun('zfs', 'destroy', '-Rf', 'testpool');
        echo $this->process->mayRun('zpool', 'destroy', '-f', 'testpool');
        echo $this->process->mayRun('rm', '-f', $this->testRoot . '/testdisk');
        echo $this->process->run('fallocate', '-l', '2G', $this->testRoot . '/testdisk');
        echo $this->process->run('zpool', 'create', 'testpool', $this->testRoot . '/testdisk');
        echo $this->process->mayRun('docker', 'network', 'create', 'n1');

        $this->resetBufferedLoggerHandler();
    }

    private function setDependentServices(string $testConfigurationName): void
    {
        $this->configurationService = new ConfigurationService(sprintf('%s/data/%s/sylar.yaml', __DIR__, $testConfigurationName));
        $this->setService(ConfigurationServiceInterface::class, $this->configurationService);
        $this->serviceCloneService = $this->getService(ServiceClonerServiceInterface::class);
        $this->containerCreationService = $this->getService(ContainerCreationServiceInterface::class);
        $this->containerExecService = $this->getService(ContainerExecServiceInterface::class);
        $this->containerFinderService = $this->getService(ContainerFinderServiceInterface::class);
        $this->containerStopService = $this->getService(ContainerStopServiceInterface::class);
    }

    protected function tearDown(): void
    {
        $this->resetBufferedLoggerHandler();
        $this->cleanExistingDockers();
        $this->process->run('zfs', 'destroy', '-Rf', 'testpool');
        $this->process->run('zpool', 'destroy', '-f', 'testpool');
        $this->process->run('rm', '-f', $this->testRoot . '/testdisk');
        $this->process->mayRun('docker', 'network', 'delete', 'n1');
    }

    private function containerExecShell(string $containerName, string $command): string
    {
        return trim($this->containerExecService->exec($containerName, 'bash', '-c', $command));
    }

    private function containerExecMysql(string $containerName, string $sql): string
    {
        return $this->containerExecShell($containerName, sprintf('mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "%s"', $sql));
    }

    private function cleanExistingDockers(): void
    {
        $this->process->mayRun('bash', '-c', 'docker rm --force $(docker ps --filter "label=environment=unit-test" -a --format "{{.ID}}")');
    }

    /**
     * @test
     */
    public function it_should_load_configuration_service_with_specific_path(): void
    {
        $this->setDependentServices('specific_configuration');
        $config = $this->configurationService->getConfiguration()->getServiceByName('mysql_specific_configuration');
        self::assertSame('ABCDEFGH', $config->getLabels()->get(1)->getValue());
    }

    /**
     * @test
     */
    public function it_should_load_configuration_service_with_commands(): void
    {
        $this->setDependentServices('commands');
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
        $this->setDependentServices('network');
        $this->serviceCloneService->startMaster('go-static-webserver');
        $this->serviceCloneService->startService('go-static-webserver', '02', 2);
        $dockerInspectionMaster = json_decode($this->process->run('docker', 'inspect', 'go-static-webserver'), true);
        $dockerInspectionService = json_decode($this->process->run('docker', 'inspect', 'go-static-webserver_02'), true);
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
        $this->setDependentServices('specific_configuration');
        $config = $this->configurationService->getConfiguration()->getServiceByName('not_existing_service_name');
        self::assertNull($config);
    }

    /**
     * @test
     */
    public function it_should_start_mysql_master_and_can_query_on_it(): void
    {
        $this->setDependentServices('start_master');
        $this->serviceCloneService->startMaster('mysql_start_master');
        $tableNames = explode(PHP_EOL, $this->containerExecMysql('mysql-start-master', 'select TABLE_NAME from information_schema.TABLES'));
        $slowQueryVariables = explode(PHP_EOL, $this->containerExecMysql('mysql-start-master', 'show variables like \'slow_query_log_file\''));

        self::assertSame('root_password', $this->containerExecShell('mysql-start-master', 'echo ${MYSQL_ROOT_PASSWORD}'));

        self::assertSame('TABLE_NAME', current($tableNames));
        self::assertContains('SESSION_VARIABLES', $tableNames);
        self::assertContains("slow_query_log_file\t/var/log/mysql/slowslow.log", $slowQueryVariables);
    }

    /**
     * @test
     */
    public function it_should_start_master_and_clones(): void
    {
        $this->setDependentServices('start_master_clones');
        $this->serviceCloneService->startMaster('mysql_start_master_clones');
        $this->containerExecMysql('mysql-start-master-clones', 'create database testdb;');
        $this->containerExecMysql('mysql-start-master-clones', <<<EOS
            create table testdb.test (
                idx INT(11) NOT NULL AUTO_INCREMENT, 
                val VARCHAR(30) NOT NULL,
                UNIQUE KEY ix_idx (idx) USING BTREE
            );
EOS);
        $this->containerExecMysql('mysql-start-master-clones', <<<EOS
            insert into testdb.test values 
            (1,'value_1'),
            (2,'value_2');
EOS);

        $this->serviceCloneService->startService('mysql_start_master_clones', '01', 1);
        $this->containerExecMysql('mysql-start-master-clones_01', <<<EOS
            insert into testdb.test values 
            (3,'value_3'),
            (4,'value_4');
EOS);
        $this->serviceCloneService->startService('mysql_start_master_clones', '02', 2);

        $this->containerExecMysql('mysql-start-master-clones', <<<EOS
            insert into testdb.test values 
            (3,'value_3');
EOS);
        $this->containerExecMysql('mysql-start-master-clones_02', <<<EOS
            insert into testdb.test values 
            (5,'value_5');
EOS);

        self::assertSame([
            "idx\tval",
            "1\tvalue_1",
            "2\tvalue_2",
          "3\tvalue_3",
        ], explode("\n", $this->containerExecMysql('mysql-start-master-clones', 'select * from testdb.test;')));

        self::assertSame([
            "idx\tval",
            "1\tvalue_1",
            "2\tvalue_2",
            "3\tvalue_3",
            "4\tvalue_4",
        ], explode("\n", $this->containerExecMysql('mysql-start-master-clones_01', 'select * from testdb.test;')));

        self::assertSame([
            "idx\tval",
            "1\tvalue_1",
            "2\tvalue_2",
            "5\tvalue_5",
        ], explode("\n", $this->containerExecMysql('mysql-start-master-clones_02', 'select * from testdb.test;')));
    }

    /**
     * @test
     */
    public function it_should_create_a_container_and_run_lifecycles_hooks(): void
    {
        $this->setDependentServices('lifecycle_hooks');
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
}
