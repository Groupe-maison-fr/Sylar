<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

use App\Infrastructure\Process\Exception\ProcessFailedException;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

final class SshProcess implements ProcessInterface
{
    private SSH2 $ssh;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        string $host,
        string $user,
        int $port,
        string $identityFile
    ) {
        $this->logger = $logger;
        $file = new File($identityFile);

        if (!$file->isFile()) {
            $errorString = sprintf('File dos not exists "%s"', $identityFile);
            $this->logger->error($errorString);
            echo $errorString . PHP_EOL;

            return;
        }

        if (!$file->isReadable()) {
            $errorString = sprintf('Unable to read "%s"', $identityFile);
            $this->logger->error($errorString);
            echo $errorString . PHP_EOL;

            return;
        }
        $key = PublicKeyLoader::load($file->getContent());

        $this->ssh = new SSH2($host, $port);
        /* @phpstan-ignore-next-line */
        if (!$this->ssh->login($user, $key)) {
            throw new \Exception('Login failed');
        }
    }

    private function getArguments(...$arguments): array
    {
        return array_merge([
            'sudo',
        ], $arguments);
    }

    public function exec(CommandDTO $command): ExecutionResultDTO
    {
        $argumentsList = implode(' ', array_filter($this->getArguments(...$command->getArguments())));
        $this->logger->debug(sprintf('Process launched "%s"', $argumentsList));

        $this->ssh->enableQuietMode();
        $output = $this->ssh->exec($argumentsList);
        $errorOutput = $this->ssh->getStdError();
        if ($command->mustRun() && $this->ssh->getExitStatus() !== 0) {
            throw new ProcessFailedException(sprintf(
                '"%s" failed with exitCode "%d", %s',
                $argumentsList,
                $this->ssh->getExitStatus(),
                $errorOutput
            ));
        }

        $this->logger->debug(sprintf('Process result "%s"', $output));
        if ($errorOutput) {
            $this->logger->debug(sprintf('Process error output "%s"', $errorOutput));
        }

        return new ExecutionResultDTO($output, $errorOutput, $this->ssh->getExitStatus());
    }

    public function run(?string ...$arguments): ExecutionResultDTO
    {
        return $this->exec(new CommandDTO($arguments, true, true));
    }

    public function mayRun(?string ...$arguments): ExecutionResultDTO
    {
        return $this->exec(new CommandDTO($arguments, false, true));
    }
}
