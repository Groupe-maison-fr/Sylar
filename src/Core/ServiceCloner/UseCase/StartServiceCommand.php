<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Messenger\AsyncCommandInterface;

final class StartServiceCommand implements AsyncCommandInterface
{
    public function __construct(
        private string $masterName,
        private string $instanceName,
        private ?int $index,
    ) {
    }

    public function getMasterName(): string
    {
        return $this->masterName;
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    public function getIndex(): ?int
    {
        return $this->index;
    }
}
