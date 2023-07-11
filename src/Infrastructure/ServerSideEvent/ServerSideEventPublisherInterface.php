<?php

declare(strict_types=1);

namespace App\Infrastructure\ServerSideEvent;

interface ServerSideEventPublisherInterface
{
    public function publish(string $topic, mixed $data): string;
}
