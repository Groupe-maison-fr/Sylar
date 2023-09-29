<?php

declare(strict_types=1);

namespace Tests;

use App\Common\Tests\LoggerAwareTestTrait;
use App\Core\ServiceCloner\Configuration\ConfigurationService;
use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use App\Infrastructure\Docker\ContainerCreationServiceInterface;
use App\Infrastructure\Docker\ContainerExecServiceInterface;
use App\Infrastructure\Docker\ContainerFinderServiceInterface;
use App\Infrastructure\Docker\ContainerStopServiceInterface;
use App\Infrastructure\Process\ProcessInterface;

trait ServiceClonerTestTrait
{
    use LoggerAwareTestTrait;

    protected ServiceClonerServiceInterface $serviceCloneService;
    protected ContainerExecServiceInterface $containerExecService;
    protected ContainerCreationServiceInterface $containerCreationService;
    protected ConfigurationServiceInterface $configurationService;
    protected string $testRoot;
    protected ProcessInterface $process;

    protected ContainerFinderServiceInterface $containerFinderService;

    protected ContainerStopServiceInterface $containerStopService;

    protected function serviceClonerSetUp(): void
    {
        $this->initLoggerBufferedHandler($this->getService('logger'));

        $this->process = $this->getService(ProcessInterface::class);
        $this->testRoot = '/tmp';

        $this->destroyExistingInstances();
        $this->process->mayRun('docker', 'network', 'create', 'n1')->getStdOutput();

        $this->resetBufferedLoggerHandler();
    }

    protected function serviceClonerTearDown(): void
    {
        $this->resetBufferedLoggerHandler();
        $this->destroyExistingInstances();
    }

    protected function setConfigurationDependentServices(string $path, string $testConfigurationName): void
    {
        preg_match('!/tests/(.*)!', $path, $matches);
        $this->configurationService = new ConfigurationService(
            sprintf('%s/data/%s/sylar.yaml', $path, $testConfigurationName),
            sprintf('%s/tests/%s/data/%s', getenv('MOUNTED_CONFIGURATION_PATH'), $matches[1], $testConfigurationName),
            sprintf('%s/tests/%s/data/%s', getenv('CONTAINER_CONFIGURATION_PATH'), $matches[1], $testConfigurationName),
        );

        $this->setService(ConfigurationServiceInterface::class, $this->configurationService);
        $this->serviceCloneService = $this->getService(ServiceClonerServiceInterface::class);
        $this->containerCreationService = $this->getService(ContainerCreationServiceInterface::class);
        $this->containerExecService = $this->getService(ContainerExecServiceInterface::class);
        $this->containerFinderService = $this->getService(ContainerFinderServiceInterface::class);
        $this->containerStopService = $this->getService(ContainerStopServiceInterface::class);
    }

    protected function containerExecShell(string $containerName, string $command): string
    {
        return trim($this->containerExecService->exec($containerName, 'bash', '-c', $command));
    }

    protected function containerExecMysql(string $containerName, string $sql): string
    {
        return $this->containerExecShell($containerName, sprintf('mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "%s"', $sql));
    }

    protected function destroyExistingInstances(): void
    {
        $this->process->mayRun('bash', '-c', 'docker ps --all --format "{{.Names}}" --filter "name=unit-test" | while read -r x; do docker rm -f $x; done');
        $this->process->mayRun('bash', '-c', 'zfs list -t all -H -o name | grep -Z "unit-test-" | while read -r x; do zfs destroy -Rf $x; done');
        $this->process->mayRun('bash', '-c', 'docker network rm n1');
    }
}
