<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker;

use Monolog\Logger;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

final class DockerApiLogger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
        if (array_key_exists('response', $context)) {
            /** @var Request $request */
            $request = $context['request'];
            /** @var Response $response */
            $response = $context['response'];
            try {
                dump(sprintf('%s [%s] %s : %s',
                    $request->getMethod(),
                    $response->getStatusCode(),
                    $request->getUri(),
                    $request->getBody()
                ));
            } catch (NotEncodableValueException $exception) {
                dump($exception->getMessage());
            }
        }
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(Logger::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(Logger::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(Logger::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(Logger::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(Logger::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(Logger::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(Logger::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(Logger::DEBUG, $message, $context);
    }
}
