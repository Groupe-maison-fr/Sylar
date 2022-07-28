<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Exception\ContainerListBadRequestException;
use Docker\API\Exception\ContainerListInternalServerErrorException;
use Docker\API\Model\ContainerSummaryItem;
use Docker\Docker;
use Psr\Log\LoggerInterface;

final class ContainerFinderService implements ContainerFinderServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;

    public function __construct(
        Docker $dockerReadOnly,
        LoggerInterface $logger
    ) {
        $this->docker = $dockerReadOnly;
        $this->logger = $logger;
    }

    public function getDockerByName(string $dockerName): ?ContainerSummaryItem
    {
        try {
            $containers = $this->docker->containerList([
                'filters' => json_encode([
                    'name' => [$dockerName],
                ]),
                'all' => true,
            ]);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Can not get DockerByName "%s" because "%s"', $dockerName, $exception->getMessage()));

            return null;
        }

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

    public function getDockersByLabel(string $labelKey, string $labelValue): array
    {
        try {
            $containers = $this->docker->containerList([
                'filters' => json_encode([
                    'label' => [sprintf('%s=%s', $labelKey, $labelValue)],
                ]),
                'all' => true,
            ]);
        } catch (ContainerListBadRequestException | ContainerListInternalServerErrorException $exception) {
            $this->logger->error(sprintf('Can not get getDockersByLabel "%s:%s" because "%s"', $labelKey, $labelValue, $exception->getErrorResponse()->getMessage()));

            return [];
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Can not get getDockersByLabel "%s:%s" because "%s"', $labelKey, $labelValue, $exception->getMessage()));

            return [];
        }

        if (empty($containers)) {
            return [];
        }

        return array_map(fn (ContainerSummaryItem $containerSummaryItem) => substr($containerSummaryItem->getNames()[0], 1), $containers);
    }
}
