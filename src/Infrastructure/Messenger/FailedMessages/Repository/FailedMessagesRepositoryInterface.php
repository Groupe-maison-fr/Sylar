<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Repository;

use App\Infrastructure\Messenger\FailedMessages\FailedMessageDTO;

interface FailedMessagesRepositoryInterface
{
    /**
     * @return FailedMessageDTO[]
     */
    public function findAll(int $max): array;

    public function getById(int $id): FailedMessageDTO;
}
