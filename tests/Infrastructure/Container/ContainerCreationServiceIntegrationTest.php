<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Container;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerExecServiceInterface;
use App\Infrastructure\Docker\ContainerFinderServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Process\SudoProcess;
use Docker\API\Model\ContainerSummaryItem;
use Ramsey\Uuid\Uuid;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class ContainerCreationServiceIntegrationTest extends AbstractIntegrationTest
{
    private ConfigurationServiceInterface $configurationService;
    private ContainerCreationServiceInterface $containerCreationService;
    private ContainerFinderServiceInterface $containerFinderService;
    private SudoProcess $sudoProcess;
    private ContainerExecServiceInterface $containerExecService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sudoProcess = $this->getService(SudoProcess::class);
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
        file_put_contents('/tmp/' . $dockerName, $dockerName);
        $config = $this->configurationService->createServiceFromArray([
            'name' => 'mini-webserver',
            'image' => 'tobilg/mini-webserver:0.5.1',
            'environment' => [[
              'name' => 'ENV_VARIABLE_1',
              'value' => 'ENV_VALUE_1',
            ]],
            'labels' => [[
              'name' => 'environment',
              'value' => 'unit-test',
            ]],
            'ports' => [[
              'hostIp' => '0.0.0.0',
              'hostPort' => '8082/tcp',
              'containerPort' => '3000/tcp',
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
                '/tmp'
            ),
            $config
        );

        sleep(2);

        /** @var ContainerSummaryItem $containerSummaryItem */
        $containerSummaryItem = $this->containerFinderService->getDockerByName($dockerName);

        self::assertSame('unit-test', $containerSummaryItem->getLabels()['environment']);
        self::assertSame('running', $containerSummaryItem->getState());
        self::assertSame('node /app/mini-webserver.js', $containerSummaryItem->getCommand());
        self::assertCount(1, $containerSummaryItem->getPorts());
        $port = $containerSummaryItem->getPorts()[0];
        self::assertSame('0.0.0.0', $port->getIP());
        self::assertSame(8082, $port->getPublicPort());
        self::assertSame(3000, $port->getPrivatePort());
        self::assertSame('tcp', $port->getType());
        self::assertSame($dockerName, $this->containerExecService->exec($dockerName, 'sh', '-c', 'cat /app/tmp/' . $dockerName));
        $this->containerExecService->exec($dockerName, 'sh', '-c', 'rm /app/tmp/' . $dockerName);
        self::assertFalse(file_exists('/tmp/' . $dockerName));
    }

    private function cleanExistingDockers(): void
    {
        $this->sudoProcess->mayRun('bash', '-c', 'docker rm --force $(docker ps --filter "label=environment=unit-test" -a --format "{{.ID}}")');
    }
}
