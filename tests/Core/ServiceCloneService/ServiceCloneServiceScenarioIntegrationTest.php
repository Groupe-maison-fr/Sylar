<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

/**
 * @internal
 */
final class ServiceCloneServiceScenarioIntegrationTest extends AbstractServiceCloneServiceIntegrationTest
{
    /**
     * @test
     */
    public function it_should_start_mysql_master_and_can_query_on_it(): void
    {
        $this->setConfigurationDependentServices('start_master');
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
        $this->setConfigurationDependentServices('start_master_clones');
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
}
