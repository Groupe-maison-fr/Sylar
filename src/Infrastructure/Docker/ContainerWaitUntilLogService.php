<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\Endpoint\ContainerLogsUntil;
use App\Infrastructure\Docker\Stream\DockerRawStreamUntil;
use Docker\Docker;
use Psr\Log\LoggerInterface;

final class ContainerWaitUntilLogService implements ContainerWaitUntilLogServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;
    private ContainerFinderServiceInterface $containerFinderService;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger,
        ContainerFinderServiceInterface $containerFinderService
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
        $this->containerFinderService = $containerFinderService;
    }

    public function wait(ContainerParameterDTO $containerParameter, string $expression, int $timeout): void
    {
        $container = $this->containerFinderService->getDockerByName($containerParameter->getName());
        if ($container === null) {
            return;
        }

        /** @var DockerRawStreamUntil $logsStream */
        $logsStream = $this->docker->executePsr7Endpoint(new ContainerLogsUntil($container->getId(), [
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
            $this->logger->debug(sprintf('SIGALRM (%d) DockerWaitUntilLogService for %s', $signo, $containerParameter->getName()));
            $logsStream->exitWait();
        }, true);
        \pcntl_alarm($timeout);
    }

    private function getStreamMatcherCallback(string $type, string $expression, DockerRawStreamUntil $logsStream, ContainerParameterDTO $containerParameter)
    {
        return function ($buffer) use ($type, $expression, $logsStream, $containerParameter): void {
            if (preg_match($expression, $buffer)) {
                \pcntl_alarm(0);
                $logsStream->exitWait();
            }
            $this->logger->debug(sprintf('%s on "%s": %s', strtoupper($type), $containerParameter->getName(), $buffer));
        };
    }
}
