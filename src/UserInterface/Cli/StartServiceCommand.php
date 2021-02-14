<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\Exception\StartServiceException;
use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class StartServiceCommand extends Command
{
    private const ARGUMENT_SERVICE_NAME = 'serviceName';
    private const ARGUMENT_INSTANCE_NAME = 'instanceName';
    private const ARGUMENT_INSTANCE_INDEX = 'instanceIndex';

    private ServiceClonerServiceInterface $serviceClonerService;

    public function __construct(
        ServiceClonerServiceInterface $serviceClonerService
    ) {
        parent::__construct();
        $this->serviceClonerService = $serviceClonerService;
    }

    protected function configure(): void
    {
        $this->setName('service:start')
            ->setDescription('generate a dataset based on parameters')
            ->addArgument(
                self::ARGUMENT_SERVICE_NAME,
                InputArgument::REQUIRED,
                'Service name'
            )
            ->addArgument(
                self::ARGUMENT_INSTANCE_NAME,
                InputArgument::REQUIRED,
                'Instance name'
            )
            ->addArgument(
                self::ARGUMENT_INSTANCE_INDEX,
                InputArgument::OPTIONAL,
                'Instance index'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serviceName = $input->getArgument(self::ARGUMENT_SERVICE_NAME);
        $instanceName = $input->getArgument(self::ARGUMENT_INSTANCE_NAME);
        $instanceIndex = $input->getArgument(self::ARGUMENT_INSTANCE_INDEX);

        try {
            $this->serviceClonerService->startService(
                $serviceName,
                $instanceName,
                $instanceIndex === null ? null : (int) $instanceIndex
            );
        } catch (StartServiceException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}
