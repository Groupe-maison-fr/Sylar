<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\UseCase;

use App\Infrastructure\Messenger\AsyncCommandInterface;

final class ForceDestroyContainerCommand implements AsyncCommandInterface
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
