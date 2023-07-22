<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractIntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @template T
     *
     * @param T $serviceName
     *
     * @return T
     */
    protected function getService($serviceName)
    {
        return static::getContainer()->get($serviceName);
    }

    protected function setService($serviceId, $service)
    {
        return static::getContainer()->set($serviceId, $service);
    }
}
