<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Exception\ContainerListBadRequestException;
use Docker\API\Exception\ContainerListInternalServerErrorException;
use Docker\API\Model\ContainerSummaryItem;
use Docker\Docker;
use Psr\Log\LoggerInterface;

final class ContainerLabelService implements ContainerLabelServiceInterface
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

    public function getDockerLabelsByName(string $dockerName): array
    {
        try {
            /** @var ContainerSummaryItem[] $containers */
            $containers = array_filter(
                $this->docker->containerList([
                    'filters' => json_encode([
                        'name' => [$dockerName],
                    ]),
                ]),
                fn (ContainerSummaryItem $containerSummaryItem) => $containerSummaryItem->getNames()[0] === sprintf('/%s', $dockerName)
            );
        } catch (ContainerListBadRequestException | ContainerListInternalServerErrorException $exception) {
            $this->logger->error(sprintf('Can not get DockerLabelsByName "%s" because "%s"', $dockerName, $exception->getErrorResponse()->getMessage()));

            return [];
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Can not get DockerLabelsByName "%s" because "%s"', $dockerName, $exception->getMessage()));

            return [];
        }

        if (empty($containers)) {
            return [];
        }

        return current($containers)->getLabels()->getArrayCopy();
    }
}
