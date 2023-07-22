<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\CreateImageInfo;
use Docker\API\Model\ImageSummary;
use Docker\Docker;
use Docker\Stream\CreateImageStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final readonly class ContainerImageService implements ContainerImageServiceInterface
{
    public function __construct(
        private Docker $dockerReadWrite,
        private LoggerInterface $logger,
    ) {
    }

    public function imageExists(string $imageName): bool
    {
        $existingImages = $this->dockerReadWrite->imageList([
            'filters' => json_encode([
                'reference' => [$imageName],
            ]),
        ]);
        if ($existingImages === null) {
            return false;
        }

        return count($this->getFilteredImages($imageName, $existingImages)) > 0;
    }

    public function pullImage(string $imageName): bool
    {
        /** @var CreateImageStream $buildStream */
        $buildStream = $this->dockerReadWrite->imageCreate($imageName, ['fromImage' => $imageName]);
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
                $frame->getProgressDetail() ? $frame->getProgressDetail()->getTotal() : '',
            ));

            if ($frame->getError()) {
                $lastIsError = true;
            }
        });
        $buildStream->wait();

        return !$lastIsError;
    }

    /**
     * @param ImageSummary[]|ResponseInterface $existingImages
     *
     * @return ImageSummary[]|ResponseInterface
     */
    public function getFilteredImages(string $imageName, array|ResponseInterface $existingImages): ResponseInterface|array
    {
        return array_filter($existingImages, function (ImageSummary $imageSummary) use ($imageName): bool {
            if (str_contains($imageName, ':')) {
                return in_array($imageName, $imageSummary->getRepoTags());
            }
            foreach ($imageSummary->getRepoTags() as $tag) {
                if (str_starts_with($tag, $imageName . ':')) {
                    return true;
                }
            }

            return false;
        });
    }
}
