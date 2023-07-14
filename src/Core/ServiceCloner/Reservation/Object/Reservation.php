<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Reservation\Object;

use Micoli\Elql\Metadata\Unique;

#[Unique('[record.getService(),record.getIndex()]', 'unique service/index')]
#[Unique('[record.getService(),record.getName()]', 'unique service/name')]
final class Reservation
{
    public function __construct(
        private string $service,
        private string $name,
        private int $index,
    ) {
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
