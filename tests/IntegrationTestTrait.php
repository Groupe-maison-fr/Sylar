<?php

declare(strict_types=1);

namespace Tests;

trait IntegrationTestTrait
{
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
