<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Docker\Docker;
use Docker\Endpoint\ContainerLogsUntil;
use Docker\Stream\DockerRawStreamUntil;
use Psr\Log\LoggerInterface;

final class ContainerWaitUntilLogService implements ContainerWaitUntilLogServiceInterface
{
    public function __construct(
        private Docker $dockerReadWrite,
        private LoggerInterface $logger,
        private ContainerFinderServiceInterface $containerFinderService,
    ) {
    }

    public function wait(ContainerParameterDTO $containerParameter, string $expression, int $timeout): void
    {
        $container = $this->containerFinderService->getDockerByName($containerParameter->name);
        if ($container === null) {
            return;
        }
        /** @var DockerRawStreamUntil $logsStream */
        $logsStream = $this->dockerReadWrite->executeEndpoint(new ContainerLogsUntil($container->getId(), [
            'stdout' => true,
            'stderr' => true,
            'follow' => true,
        ]));

        $logsStream->onStdout($this->getStreamMatcherCallback('stdout', $expression, $logsStream, $containerParameter));
        $logsStream->onStderr($this->getStreamMatcherCallback('stderr', $expression, $logsStream, $containerParameter));
        $this->initTimeoutAlarm($timeout, $logsStream, $containerParameter);
        $logsStream->wait();
    }

    private function initTimeoutAlarm(int $timeout, DockerRawStreamUntil $logsStream, ContainerParameterDTO $containerParameter): void
    {
        \pcntl_signal(SIGALRM, function (int $signo) use ($logsStream, $containerParameter): void {
            $this->logger->debug(sprintf('SIGALRM (%d) DockerWaitUntilLogService for %s', $signo, $containerParameter->name));
            $logsStream->exitWait();
        }, true);
        \pcntl_alarm($timeout);
    }

    private function getStreamMatcherCallback(string $type, string $expression, DockerRawStreamUntil $logsStream, ContainerParameterDTO $containerParameter): callable
    {
        return function ($buffer) use ($type, $expression, $logsStream, $containerParameter): void {
            if (preg_match($expression, $buffer)) {
                \pcntl_alarm(0);
                $logsStream->exitWait();
            }
            $this->logger->debug(sprintf('%s on "%s": %s', strtoupper($type), $containerParameter->name, $buffer));
        };
    }
}
