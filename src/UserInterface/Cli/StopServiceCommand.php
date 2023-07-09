<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\Exception\StopServiceException;
use App\Core\ServiceCloner\UseCase\StopServiceCommand as StopServiceBusCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('service:stop', description: 'Stop a replicated service')]
final class StopServiceCommand extends Command
{
    private const ARGUMENT_SERVICE_NAME = 'serviceName';
    private const ARGUMENT_INSTANCE_NAME = 'instanceName';

    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        parent::__construct();
        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_SERVICE_NAME,
                InputArgument::REQUIRED,
                'Service name',
            )
            ->addArgument(
                self::ARGUMENT_INSTANCE_NAME,
                InputArgument::REQUIRED,
                'Instance name',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serviceName = $input->getArgument(self::ARGUMENT_SERVICE_NAME);
        $instanceName = $input->getArgument(self::ARGUMENT_INSTANCE_NAME);

        try {
            $this->messageBus->dispatch(new StopServiceBusCommand(
                $serviceName,
                $instanceName,
            ));
        } catch (StopServiceException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}
