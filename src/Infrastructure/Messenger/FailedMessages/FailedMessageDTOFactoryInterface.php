<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages;

use Symfony\Component\Messenger\Envelope;

interface FailedMessageDTOFactoryInterface
{
    public function create(Envelope $envelope): FailedMessageDTO;
}
