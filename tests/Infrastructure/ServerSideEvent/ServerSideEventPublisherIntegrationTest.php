<?php

declare(strict_types=1);

namespace Tests\Infrastructure\ServerSideEvent;

use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class ServerSideEventPublisherIntegrationTest extends AbstractIntegrationTest
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
