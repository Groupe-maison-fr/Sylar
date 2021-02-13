<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\CreateImageInfo;
use Docker\API\Model\ImageSummary;
use Docker\Docker;
use Docker\Stream\CreateImageStream;
use Psr\Log\LoggerInterface;

final class ContainerImageService implements ContainerImageServiceInterface
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

    public function imageExists(string $imageName): bool
    {
        $existingImages = $this->docker->imageList([
            'filters' => json_encode([
                'reference' => [$imageName],
            ]),
        ]);
        if ($existingImages === null) {
            return false;
        }

        return count(array_filter($existingImages, function (ImageSummary $imageSummary) use ($imageName) {
            return in_array($imageName, $imageSummary->getRepoTags());
        })) > 0;
    }

    public function pullImage(string $imageName): bool
    {
        /** @var CreateImageStream $buildStream */
        $buildStream = $this->docker->imageCreate($imageName, ['fromImage' => $imageName]);
        if ($buildStream == null) {
            $this->logger->error(sprintf('Error on imageCreate %s', $imageName));

            return false;
        }
        $lastIsError = false;
        $buildStream->onFrame(function (CreateImageInfo $frame) use ($imageName, &$lastIsError): void {
            $this->logger->debug(sprintf(
                '%s: %s %s %s/%s',
                $imageName,
                $frame->getStatus(),
                $frame->getProgress(),
                $frame->getProgressDetail() ? $frame->getProgressDetail()->getCurrent() : '',
                $frame->getProgressDetail() ? $frame->getProgressDetail()->getTotal() : ''
            ));

            if ($frame->getError()) {
                $lastIsError = true;
            }
        });
        $buildStream->wait();

        return !$lastIsError;
    }
}
