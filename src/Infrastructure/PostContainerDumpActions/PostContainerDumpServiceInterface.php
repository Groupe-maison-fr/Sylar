<?php

declare(strict_types=1);

namespace App\Infrastructure\PostContainerDumpActions;

interface PostContainerDumpServiceInterface
{
    public function execute(): void;
}
