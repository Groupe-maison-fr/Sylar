<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
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

    /**
     * @var ConfigurationServiceInterface
     */
    private $dockerConfiguration;

    /**
     * @var ServiceClonerServiceInterface
     */
    private $serviceClonerService;

    public function __construct(
        ConfigurationServiceInterface $dockerConfiguration,
        ServiceClonerServiceInterface $serviceClonerService
    ) {
        parent::__construct();
        $this->dockerConfiguration = $dockerConfiguration;
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
                InputArgument::REQUIRED,
                'Instance index'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serviceName = $input->getArgument(self::ARGUMENT_SERVICE_NAME);
        $instanceName = $input->getArgument(self::ARGUMENT_INSTANCE_NAME);
        $instanceIndex = $input->getArgument(self::ARGUMENT_INSTANCE_INDEX);

        $serviceConfiguration = $this->dockerConfiguration->getConfiguration()->getServiceByName($serviceName);
        if ($serviceConfiguration === null) {
            $output->writeln(sprintf('<error>Service name %s does not exists</error>', $serviceName));

            return 1;
        }

        $this->serviceClonerService->start($serviceName, $instanceName, (int) $instanceIndex);

        return 0;
    }
}
