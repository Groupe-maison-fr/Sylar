<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use Tests\AbstractIntegrationTestCase;
use Tests\ServiceClonerTestTrait;

abstract class AbstractServiceCloneServiceIntegrationTestCase extends AbstractIntegrationTestCase
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
}
