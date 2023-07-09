<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Messenger\AsyncCommandInterface;

final class ForceDestroyContainerCommand implements AsyncCommandInterface
{
    private string $name;

    public function __construct(
        string $name,
    ) {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
