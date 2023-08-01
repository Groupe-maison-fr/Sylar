<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractIntegrationTestCase extends KernelTestCase
{
    use IntegrationTestTrait;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
