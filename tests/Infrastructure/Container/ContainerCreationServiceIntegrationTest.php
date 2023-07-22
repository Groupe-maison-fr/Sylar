<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Container;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerExecServiceInterface;
use App\Infrastructure\Docker\ContainerFinderServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Process\ProcessInterface;
use Docker\API\Model\ContainerSummaryItem;
use Ramsey\Uuid\Uuid;
use Tests\AbstractIntegrationTestCase;

/**
 * @internal
 */
final class ContainerCreationServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private ConfigurationServiceInterface $configurationService;
    private ContainerCreationServiceInterface $containerCreationService;
    private ContainerFinderServiceInterface $containerFinderService;
    private ProcessInterface $sudoProcess;
    private ContainerExecServiceInterface $containerExecService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sudoProcess = $this->getService(ProcessInterface::class);
        $this->configurationService = $this->getService(ConfigurationServiceInterface::class);
        $this->containerCreationService = $this->getService(ContainerCreationServiceInterface::class);
        $this->containerFinderService = $this->getService(ContainerFinderServiceInterface::class);
        $this->containerExecService = $this->getService(ContainerExecServiceInterface::class);
        $this->cleanExistingDockers();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanExistingDockers();
    }

    /**
     * @test
     */
    public function it_should_create_a_container_from_config(): void
    {
        $dockerName = 'unit-test-' . Uuid::uuid4()->toString();
        // file_put_contents(sprintf('/tmp/%s', $dockerName), $dockerName);
        $config = $this->configurationService->createServiceFromArray([
            'name' => 'mini-webserver',
            'image' => 'nginx',
            'environments' => [[
              'name' => 'ENV_VARIABLE_1',
              'value' => 'ENV_VALUE_1',
            ]],
            'labels' => [[
              'name' => 'environment',
              'value' => 'unit-test',
            ]],
            'ports' => [[
              'hostIp' => '0.0.0.0',
              'hostPort' => '8198/tcp',
              'containerPort' => '80/tcp',
            ]],
            'mounts' => [[
              'source' => '/tmp',
              'target' => '/app/tmp',
            ]],
        ]);
        $this->containerCreationService->createDocker(
            new ContainerParameterDTO(
                $dockerName,
                0,
                '/tmp',
            ),
            $config,
            ['sylar-label' => '123'],
        );

        sleep(2);
        /** @var ContainerSummaryItem $containerSummaryItem */
        $containerSummaryItem = $this->containerFinderService->getDockerByName($dockerName);
        self::assertNotNull($containerSummaryItem);
        self::assertSame('unit-test', $containerSummaryItem->getLabels()['environment']);
        self::assertSame('running', $containerSummaryItem->getState());
        self::assertSame('/docker-entrypoint.sh nginx -g \'daemon off;\'', $containerSummaryItem->getCommand());
        self::assertCount(1, $containerSummaryItem->getPorts());
        $port = $containerSummaryItem->getPorts()[0];
        self::assertSame('0.0.0.0', $port->getIP());
        self::assertSame(8198, $port->getPublicPort());
        self::assertSame(80, $port->getPrivatePort());
        self::assertSame('tcp', $port->getType());
        self::assertArrayHasKey('sylar-label', $containerSummaryItem->getLabels());
        self::assertSame('123', $containerSummaryItem->getLabels()['sylar-label']);
        $this->containerExecService->exec($dockerName, 'echo ' . $dockerName);
        //        self::assertSame($dockerName, trim(file_get_contents(sprintf('/tmp/%s', $dockerName))));
        //        self::assertSame($dockerName, trim($this->containerExecService->exec($dockerName, 'cat', '/app/tmp/' . $dockerName)));
        //        $this->containerExecService->exec($dockerName, 'sh', '-c', 'rm /app/tmp/' . $dockerName);
        //        self::assertFalse(file_exists(sprintf('/tmp/%s', $dockerName)));
    }

    private function cleanExistingDockers(): void
    {
        $this->sudoProcess->mayRun('bash', '-c', 'docker rm --force $(docker ps --filter "label=environment=unit-test" -a --format "{{.ID}}")');
    }
}
