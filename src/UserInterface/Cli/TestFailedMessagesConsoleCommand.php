<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\TransportInterface;

#[AsCommand('debug:messenger:failed-messages-fixtures')]
final class TestFailedMessagesConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@messenger.transport.failed')]
        private TransportInterface $failedTransport,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commands = $this->getInstances($this->getClasses(), 20);
        $exceptions = $this->getInstances($this->getExceptions(), 20);

        for ($a = 0; $a < 40; ++$a) {
            $sentToFailureStamp = new SentToFailureTransportStamp('async');
            $redeliveryStamp1 = new RedeliveryStamp(0);
            $errorStamp = ErrorDetailsStamp::create($exceptions[random_int(0, count($exceptions) - 1)]);
            $redeliveryStamp2 = new RedeliveryStamp(0);
            $envelope = new Envelope($commands[random_int(0, count($commands) - 1)], [
                new TransportMessageIdStamp(15),
                $sentToFailureStamp,
                $redeliveryStamp1,
                $errorStamp,
                $redeliveryStamp2,
            ]);
            $output->write('.');
            $this->failedTransport->send($envelope);
        }

        return 0;
    }

    /**
     * @return SplFileInfo[]
     **/
    private function getClasses(): array
    {
        $finder = new Finder();
        $files = iterator_to_array(
            $finder
                ->in(__DIR__ . '/../../../src/Core/')
                ->name('!.*Command\.php$!')
                ->notName('!.*ConsoleCommand\.php$!')
                ->getIterator(),
        );
        shuffle($files);

        return $files;
    }

    /**
     * @return SplFileInfo[]
     **/
    private function getExceptions(): array
    {
        $finder = new Finder();
        $files = iterator_to_array(
            $finder
                ->in([__DIR__ . '/../../../src/Core/'])
                ->name('!.*Exception\.php$!')
                ->getIterator(),
        );
        shuffle($files);

        return $files;
    }

    /**
     * @param SplFileInfo[] $filenames
     *
     * @return object[]
     **/
    private function getInstances(array $filenames, int $maximum): array
    {
        $instances = [];
        if (count($filenames) === 0) {
            return $instances;
        }
        do {
            $file = array_pop($filenames);
            $command = $this->getInstance($file->getRealPath());
            if ($command !== null) {
                $instances[] = $command;
            }
        } while (count($instances) <= $maximum && count($filenames) > 0);

        return $instances;
    }

    private function getInstance(string $filename): ?object
    {
        $className = str_replace(['/app/src', '/', '.php'], ['App', '\\', ''], $filename);
        $reflectionClass = new ReflectionClass($className);
        $arguments = [];
        if ($reflectionClass->getConstructor() === null) {
            return null;
        }
        if (empty($reflectionClass->getConstructor()->getParameters())) {
            return $reflectionClass->newInstanceArgs([]);
        }
        try {
            foreach ($reflectionClass->getConstructor()->getParameters() as $argument) {
                /** @var ?ReflectionNamedType $reflectionNamedType */
                $reflectionNamedType = $argument->getType();
                if ($reflectionNamedType === null) {
                    return $reflectionClass->newInstanceArgs([]);
                }
                $arguments[] = match ($reflectionNamedType->getName()) {
                    'string' => $this->generateRandomString(5, 20),
                    'int' => random_int(1, 100),
                    'DateTimeImmutable' => new DateTimeImmutable(),
                    'bool' => true,
                    'float' => random_int(100, 1000) / random_int(3, 12),
                    'Throwable' => new Exception(),
                    'array' => throw new InvalidArgumentException(),
                    default => throw new InvalidArgumentException()
                };
            }

            return $reflectionClass->newInstanceArgs($arguments);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    private function generateRandomString(int $min, int $max): string
    {
        return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, random_int($min, $max));
    }
}
