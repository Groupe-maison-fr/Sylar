<?php

declare(strict_types=1);

namespace App\Infrastructure\ServerSideEvent;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class ServerSideEventPublisher implements ServerSideEventPublisherInterface
{
    public function __construct(
        private HubInterface $hub,
    ) {
    }

    public function publish(string $topic, mixed $data): string
    {
        return $this->hub->publish(new Update($topic, json_encode($data, JSON_PRETTY_PRINT)));
    }
}
