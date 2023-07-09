<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Messenger\AsyncCommandInterface;

final class StartMasterServiceCommand implements AsyncCommandInterface
{
    public function __construct(
        private string $masterName,
    ) {
    }

    public function getMasterName(): string
    {
        return $this->masterName;
    }
}
