<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration;

use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;

interface ConfigurationServiceInterface
{
    public function getConfiguration(): ServiceCloner;

    public function createServiceClonerFromArray(array $data): ServiceCloner;

    public function createServiceFromArray(array $data): Service;
}
