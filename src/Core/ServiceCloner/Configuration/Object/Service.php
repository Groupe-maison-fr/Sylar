<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class Service
{
    public function __construct(
        public string $name = '',
        public string $image = '',
        public string $command = '',
        public ?string $entryPoint = null,
        public ?string $networkMode = null,
        public ?LifeCycleHooks $lifeCycleHooks = null,
        /** @var Environment[] */
        public array $environments = [],
        /** @var Mount[] */
        public array $mounts = [],
        /** @var Port[] */
        public array $ports = [],
        /** @var Label[] */
        public array $labels = [],
    ) {
    }
}
