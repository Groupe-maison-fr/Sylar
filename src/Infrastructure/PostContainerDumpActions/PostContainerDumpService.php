<?php

declare(strict_types=1);

namespace App\Infrastructure\PostContainerDumpActions;

final class PostContainerDumpService implements PostContainerDumpServiceInterface
{
    private array $actions;

    public function __construct(PostContainerDumpServiceInterface ...$actions)
    {
        $this->actions = $actions;
    }

    public function execute(): void
    {
        foreach ($this->actions as $action) {
            $action->execute();
        }
    }
}
