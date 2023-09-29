<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class Port
{
    public function __construct(
        public string $containerPort,
        public string $hostPort,
        public string $hostIp,
    ) {
    }
}
