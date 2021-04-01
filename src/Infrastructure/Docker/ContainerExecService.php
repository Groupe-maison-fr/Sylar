<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\Stream\DockerRawStream;
use Psr\Log\LoggerInterface;

final class ContainerExecService implements ContainerExecServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;
    private ContainerFinderServiceInterface $dockerFinderService;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger,
        ContainerFinderServiceInterface $dockerFinderService
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
        $this->dockerFinderService = $dockerFinderService;
    }

    public function exec(string $dockerName, string ...$arguments): string
    {
        $container = $this->dockerFinderService->getDockerByName($dockerName);
        if ($container === null) {
            return '';
        }
        $execConfig = new ContainersIdExecPostBody();
        $execConfig
            ->setAttachStdout(true)
            ->setAttachStderr(true)
            ->setCmd($arguments);

        $execCreateResult = $this->docker->containerExec($container->getId(), $execConfig);

        $execStartConfig = new ExecIdStartPostBody();
        $execStartConfig->setDetach(false);
        $execStartConfig->setTty(false);

        /** @var DockerRawStream $stream */
        $stream = $this->docker->execStart($execCreateResult->getId(), $execStartConfig);
        $stdoutFull = '';
        $stream->onStdout(function ($stdout) use (&$stdoutFull): void {
            $stdoutFull .= $stdout;
        });
        $stream->wait();

        return $stdoutFull;
    }
}
