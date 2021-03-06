<?php

declare(strict_types=1);

namespace App\Infrastructure\Process;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

final class Process implements ProcessInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    private function exec(bool $must, ...$arguments): string
    {
        $argumentList = $this->flattenArguments(...$arguments);
        $this->logger->debug(sprintf('Process launched "%s"', implode(' ', $argumentList)));

        $process = new SymfonyProcess($argumentList);
        if ($must) {
            $process->mustRun();
        } else {
            $process->run();
        }
        $output = $process->getOutput();

        $this->logger->debug(sprintf('Process result "%s"', $output));

        return $output;
    }

    public function run(...$arguments): string
    {
        return $this->exec(true, ...$arguments);
    }

    public function mayRun(...$arguments): string
    {
        return $this->exec(false, ...$arguments);
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
