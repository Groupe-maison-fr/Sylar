<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\Stream\DockerRawStream;
use DomainException;
use Psr\Log\LoggerInterface;

final class ContainerExecService implements ContainerExecServiceInterface
{
    public function __construct(
        private Docker $dockerReadWrite,
        private LoggerInterface $logger,
        private ContainerFinderServiceInterface $dockerFinderService,
    ) {
    }

    public function exec(string $dockerName, string ...$arguments): string
    {
        $container = $this->dockerFinderService->getDockerByName($dockerName);
        if ($container === null) {
            throw new DomainException(sprintf('Container %s not found', $dockerName));
        }
        $execConfig = new ContainersIdExecPostBody();
        $execConfig
            ->setAttachStdout(true)
            ->setAttachStderr(true)
            ->setCmd($arguments);

        $execCreateResult = $this->dockerReadWrite->containerExec($container->getId(), $execConfig);

        $execStartConfig = new ExecIdStartPostBody();
        $execStartConfig->setDetach(false);
        $execStartConfig->setTty(false);

        /** @var DockerRawStream $stream */
        $stream = $this->dockerReadWrite->execStart($execCreateResult->getId(), $execStartConfig);
        $stdoutFull = '';
        $stream->onStdout(function ($stdout) use (&$stdoutFull): void {
            $stdoutFull .= $stdout;
        });
        $stream->wait();

        return $stdoutFull;
    }
}
