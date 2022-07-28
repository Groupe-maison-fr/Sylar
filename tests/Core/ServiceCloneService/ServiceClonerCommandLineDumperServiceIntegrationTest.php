<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Core\ServiceCloner\Configuration\ConfigurationService;
use App\Core\ServiceCloner\ServiceClonerCommandLineDumperService;
use App\Infrastructure\Docker\ContainerParameter\ConfigurationExpressionGenerator;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class ServiceClonerCommandLineDumperServiceIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function it_should_do_get_an_command_line_representing_a_configuration(): void
    {
        preg_match('!/tests/(.*)!', __DIR__, $matches);
        $testConfigurationName = 'start_master';
        $configurationService = new ConfigurationService(
            sprintf('%s/data/%s/sylar.yaml', __DIR__, $testConfigurationName),
            sprintf('%s/tests/%s/data/%s', getenv('MOUNTED_CONFIGURATION_PATH'), $matches[1], $testConfigurationName),
            sprintf('%s/tests/%s/data/%s', getenv('CONTAINER_CONFIGURATION_PATH'), $matches[1], $testConfigurationName)
        );
        $configurationExpressionGenerator = new ConfigurationExpressionGenerator($configurationService);
        $serviceClonerCommandLineDumperService = new ServiceClonerCommandLineDumperService($configurationService, $configurationExpressionGenerator);
        $containerParameter = new ContainerParameterDTO(
            'mysql-test',
            0,
            'toto/tata'
        );

        self::assertSame($this->getExpectedLifecycleHooksString(), $serviceClonerCommandLineDumperService->dump($containerParameter));
    }

    private function getExpectedLifecycleHooksString(): string
    {
        return 'docker run --env MYSQL_ROOT_PASSWORD=root_password --env MYSQL_USER=user' .
            ' --env MYSQL_PASSWORD=password --env MYSQL_DATABASE=roketto --env MYSQL_INITDB_SKIP_TZINFO=1' .
            ' --env CLONE_NAME=mysql-test --env CLONE_INDEX=0 --env CLONE_REPLICATED_FILESYSTEM=toto/tata' .
            ' --mount type=bind,target=/var/lib/mysql,source=toto/tata --mount type=bind,target=/app,source=/opt/sylar/tests/Core/ServiceCloneService/data/start_master' .
            ' --mount type=bind,target=/etc/mysql/conf.d,source=/opt/sylar/tests/Core/ServiceCloneService/data/start_master/mysql/etc/mysql/conf.d' .
            ' --publish 0.0.0.0:3406:3306/tcp --label environment=unit-test --net=n1 --name mysql-test --detach library/mariadb:10.5.3';
    }
}
