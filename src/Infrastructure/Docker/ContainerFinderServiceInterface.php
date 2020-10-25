<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\ContainerSummaryItem;

interface ContainerFinderServiceInterface
{
    public function getDockerByName(string $dockerName): ?ContainerSummaryItem;
}
