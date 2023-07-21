<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final readonly class LifeCycleHooks
{
    public function __construct(
        /** @var PreStartCommand[] */
        public array $preStartCommands = [],
        /** @var PostStartWaiter[] */
        public array $postStartWaiters = [],
        /** @var PostStartCommand[] */
        public array $postStartCommands = [],
        /** @var PostDestroyCommand[] */
        public array $postDestroyCommands = [],
    ) {
    }
}
