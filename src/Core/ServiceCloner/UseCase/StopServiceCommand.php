<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Messenger\AsyncCommandInterface;

final class StopServiceCommand implements AsyncCommandInterface
{
    private string $masterName;

    private string $instanceName;

    public function __construct(
        string $masterName,
        string $instanceName
    ) {
        $this->masterName = $masterName;
        $this->instanceName = $instanceName;
    }

    public function getMasterName(): string
    {
        return $this->masterName;
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }
}
