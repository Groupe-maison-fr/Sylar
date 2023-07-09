<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

use App\Infrastructure\Process\Exception\ProcessFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

final class Process implements ProcessInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function exec(CommandDTO $command): ExecutionResultDTO
    {
        $argumentList = $this->flattenArguments(...$command->getArguments());
        if ($command->sudo()) {
            array_unshift($argumentList, 'sudo');
        }
        $this->logger->debug(sprintf('Process launched "%s"', implode(' ', $argumentList)));

        $process = new SymfonyProcess($argumentList);
        $process->run();
        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        $this->logger->debug(sprintf('Process result "%s"', $output));
        if ($errorOutput) {
            $this->logger->debug(sprintf('Process error output "%s"', $errorOutput));
        }

        if ($command->mustRun() && $process->getExitCode() !== 0) {
            throw new ProcessFailedException(sprintf(
                '"%s" failed with exitCode "%d", %s',
                implode(' ', $command->getArguments()),
                $process->getExitCode(),
                $errorOutput,
            ));
        }

        return new ExecutionResultDTO($output, $errorOutput, $process->getExitCode());
    }

    public function run(?string ...$arguments): ExecutionResultDTO
    {
        return $this->exec(new CommandDTO($arguments, true, true));
    }

    public function mayRun(?string ...$arguments): ExecutionResultDTO
    {
        return $this->exec(new CommandDTO($arguments, false, true));
    }

    private function flattenArguments(...$argumentList): array
    {
        return array_reduce($argumentList, function (array $arguments, $argument) {
            if (is_array($argument)) {
                return array_merge($arguments, $argument);
            }
            if ($argument === null) {
                return $arguments;
            }

            $arguments[] = $argument;

            return $arguments;
        }, []);
    }
}
