<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\Endpoint;

use App\Infrastructure\Docker\Stream\DockerRawStreamUntil;
use Docker\API\Endpoint\ContainerLogs as BaseEndpoint;
use Jane\OpenApiRuntime\Client\Client;
use Jane\OpenApiRuntime\Client\Exception\InvalidFetchModeException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ContainerLogsUntil extends BaseEndpoint
{
    /**
     * @return DockerRawStreamUntil|ResponseInterface
     */
    public function parsePSR7Response(ResponseInterface $response, SerializerInterface $serializer, string $fetchMode = Client::FETCH_OBJECT)
    {
        if ($fetchMode === Client::FETCH_OBJECT) {
            if ($response->getStatusCode() === 200) {
                return new DockerRawStreamUntil($response->getBody());
            }

            /* @phpstan-ignore-next-line */
            return $this->transformResponseBody((string) $response->getBody(), $response->getStatusCode(), $serializer);
        }

        if ($fetchMode === Client::FETCH_RESPONSE) {
            return $response;
        }

        throw new InvalidFetchModeException(\sprintf('Fetch mode %s is not supported', $fetchMode));
    }
}
