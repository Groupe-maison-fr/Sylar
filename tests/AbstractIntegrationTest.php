<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractIntegrationTest extends KernelTestCase
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

    protected function getService($serviceName)
    {
        return static::$container->get($serviceName);
    }

    protected function setService($serviceId, $service)
    {
        return static::$container->set($serviceId, $service);
    }
}
