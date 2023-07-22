<?php

declare(strict_types=1);

namespace Tests\Infrastructure\ServerSideEvent;

use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Tests\AbstractIntegrationTestCase;

/**
 * @internal
 */
final class ServerSideEventPublisherIntegrationTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     */
    public function it_should_publish_event(): void
    {
        $publisher = $this->getService(ServerSideEventPublisherInterface::class);
        self::assertNotEmpty('urn:', $publisher->publish(
            'sylar',
            ['status' => true],
        ));
    }
}
