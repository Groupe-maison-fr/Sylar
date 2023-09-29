<?php

declare(strict_types=1);

namespace Tests\UserInterface\GraphQL\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use Micoli\Trail\Trail;
use Tests\GraphQlTestCase;
use Tests\ServiceClonerTestTrait;

/**
 * @internal
 */
class ServiceClonerGraphQlTest extends GraphQlTestCase
{
    use ServiceClonerTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceClonerSetUp();
    }

    protected function tearDown(): void
    {
        $this->serviceClonerTearDown();
    }

    /**
     * @test
     */
    public function it_should_start_master_and_clones_though_graphql(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'start_master_clones');
        $this->graphQlSetService(ConfigurationServiceInterface::class, $this->configurationService);

        $result = $this->getInstancesThroughGraphQlQuery();
        self::assertSame('unit-test-mysql-start-master-clones', $result['name']);
        self::assertCount(0, $result['containers']);

        $this->serviceCloneService->startMaster('unit-test-mysql-start-master-clones');
        self::assertCount(1, $this->getInstancesThroughGraphQlQuery()['containers']);
        self::assertMatchesPattern([
            [
                'containerName' => 'unit-test-mysql-start-master-clones',
                'instanceName' => 'master',
                'instanceIndex' => 0,
                'time' => '@integer@',
                'uptime' => '@integer@',
            ],
        ], $this->getInstancesThroughGraphQlQuery()['containers']);

        $result = $this->graphQlStartService('unit-test-mysql-start-master-clones', 'instance-01', 1);
        self::assertTrue(Trail::eval($result, '[data][startService][success]'));
        self::assertMatchesPattern([
            [
                'containerName' => 'unit-test-mysql-start-master-clones',
                'instanceName' => 'master',
                'instanceIndex' => 0,
                'time' => '@integer@',
                'uptime' => '@integer@',
            ],
            [
                'containerName' => 'unit-test-mysql-start-master-clones_instance-01',
                'instanceName' => 'instance-01',
                'instanceIndex' => 1,
                'time' => '@integer@',
                'uptime' => '@integer@',
            ],
        ], $this->getInstancesThroughGraphQlQuery()['containers']);

        $result = $this->graphQlStartService('unit-test-mysql-start-master-clones', 'instance-02', 2);
        self::assertTrue(Trail::eval($result, '[data][startService][success]'), json_encode($result));
        self::assertMatchesPattern([
                'containerName' => 'unit-test-mysql-start-master-clones_instance-02',
                'instanceName' => 'instance-02',
                'instanceIndex' => 2,
                'time' => '@integer@',
                'uptime' => '@integer@',
        ], Trail::eval($this->getInstancesThroughGraphQlQuery(), '[containers]|@last'));

        $result = $this->graphQlStopService('unit-test-mysql-start-master-clones', 'instance-01');
        self::assertTrue(Trail::eval($result, '[data][stopService][success]'));

        self::assertMatchesPattern([
            [
                'containerName' => 'unit-test-mysql-start-master-clones',
                'instanceName' => 'master',
                'instanceIndex' => 0,
                'time' => '@integer@',
                'uptime' => '@integer@',
            ],
            [
                'containerName' => 'unit-test-mysql-start-master-clones_instance-02',
                'instanceName' => 'instance-02',
                'instanceIndex' => 2,
                'time' => '@integer@',
                'uptime' => '@integer@',
            ],
        ], $this->getInstancesThroughGraphQlQuery()['containers']);
    }

    /**
     * @test
     */
    public function it_should_start_master_and_clones_and_restart_clone_though_graphql(): void
    {
        $this->setConfigurationDependentServices(__DIR__, 'start_master_clones');
        $this->graphQlSetService(ConfigurationServiceInterface::class, $this->configurationService);

        $result = $this->getInstancesThroughGraphQlQuery();
        self::assertSame('unit-test-mysql-start-master-clones', $result['name']);
        self::assertCount(0, $result['containers']);

        $this->serviceCloneService->startMaster('unit-test-mysql-start-master-clones');

        $result = $this->graphQlStartService('unit-test-mysql-start-master-clones', 'instance-01', 1);
        self::assertTrue(Trail::eval($result, '[data][startService][success]'));
        $firstInstanceStartTime = Trail::eval($this->getInstancesThroughGraphQlQuery(), '[containers]|@last|[time]');
        $result = $this->graphQlRestartService('unit-test-mysql-start-master-clones', 'instance-01', 1);
        self::assertTrue(Trail::eval($result, '[data][restartService][success]'));
        $secondInstanceStartTime = Trail::eval($this->getInstancesThroughGraphQlQuery(), '[containers]|@last|[time]');

        self::assertTrue($secondInstanceStartTime > $firstInstanceStartTime);
    }

    private function getInstancesThroughGraphQlQuery(): array
    {
        return $this->graphQlQuery(
            <<<GRAPHQL
                  query Services {
                    services {
                      name
                      containers {
                        containerName
                        instanceName
                        instanceIndex
                        time
                        uptime
                      }
                    }
                  }
                GRAPHQL
        )['data']['services'][0];
    }

    private function graphQlStartService(string $masterName, string $instanceName, ?int $index): array
    {
        return $this->graphQlMutation(
            <<<'GRAPHQL'
                  mutation StartService(
                    $masterName: String!
                    $instanceName: String!
                    $index: Int
                  ) {
                    startService(
                        input: { masterName: $masterName, instanceName: $instanceName, index: $index }
                    ) {
                    ... on SuccessOutput {
                        success
                    }
                    ... on FailedOutput {
                        code
                        message
                    }
                  }
                }
                GRAPHQL,
            [
                'masterName' => $masterName,
                'instanceName' => $instanceName,
                'index' => $index,
            ],
        );
    }

    private function graphQlRestartService(string $masterName, string $instanceName, ?int $index): array
    {
        return $this->graphQlMutation(
            <<<'GRAPHQL'
                  mutation RestartService(
                    $masterName: String!
                    $instanceName: String!
                    $index: Int
                  ) {
                    restartService(
                        input: { masterName: $masterName, instanceName: $instanceName, index: $index }
                    ) {
                    ... on SuccessOutput {
                        success
                    }
                    ... on FailedOutput {
                        code
                        message
                    }
                  }
                }
                GRAPHQL,
            [
                'masterName' => $masterName,
                'instanceName' => $instanceName,
                'index' => $index,
            ],
        );
    }

    private function graphQlStopService(string $masterName, string $instanceName): array
    {
        return $this->graphQlMutation(
            <<<'GRAPHQL'
                  mutation StopService(
                    $masterName: String!
                    $instanceName: String!
                  ) {
                    stopService(
                        input: { masterName: $masterName, instanceName: $instanceName }
                    ) {
                    ... on SuccessOutput {
                        success
                    }
                    ... on FailedOutput {
                        code
                        message
                    }
                  }
                }
                GRAPHQL,
            [
                'masterName' => $masterName,
                'instanceName' => $instanceName,
            ],
        );
    }
}
