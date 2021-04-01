<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

interface IndexManagerServiceInterface
{
    public function getNextAvailable(): int;
}
