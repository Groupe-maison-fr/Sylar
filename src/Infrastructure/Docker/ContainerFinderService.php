<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\ContainerSummaryItem;
use Docker\Docker;
use Psr\Log\LoggerInterface;

final class ContainerFinderService implements ContainerFinderServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
    }

    public function getDockerByName(string $dockerName): ?ContainerSummaryItem
    {
        $containers = $this->docker->containerList([
            'filters' => json_encode([
                'name' => [$dockerName],
            ]),
        ]);

        if (empty($containers)) {
            return null;
        }

        $filteredContainers = array_filter(
            $containers,
            function (ContainerSummaryItem $containerSummeryItem) use ($dockerName) {
                return $containerSummeryItem->getNames()[0] === '/' . $dockerName;
            }
        );

        if (empty($filteredContainers)) {
            return null;
        }

        return current($filteredContainers);
    }
}
