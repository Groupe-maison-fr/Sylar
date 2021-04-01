<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Messenger\AsyncCommandInterface;

final class StartServiceCommand implements AsyncCommandInterface
{
    private string $masterName;

    private string $instanceName;

    private ?int $index;

    public function __construct(
        string $masterName,
        string $instanceName,
        ?int $index
    ) {
        $this->masterName = $masterName;
        $this->instanceName = $instanceName;
        $this->index = $index;
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
