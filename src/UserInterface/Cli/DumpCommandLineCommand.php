<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\ServiceClonerCommandLineDumperService;
use App\Core\ServiceCloner\ServiceClonerNamingServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class DumpCommandLineCommand extends Command
{
    private const ARGUMENT_SERVICE_NAME = 'serviceName';
    private const ARGUMENT_REPLICA_FILESYSTEM_PATH = 'instanceName';
    private const ARGUMENT_INSTANCE_INDEX = 'instanceIndex';

    private ServiceClonerStateServiceInterface $serviceClonerStateService;

    private ConfigurationServiceInterface $configurationService;

    private ServiceClonerCommandLineDumperService $serviceClonerCommandLineDumperService;

    private ServiceClonerNamingServiceInterface $serviceClonerNamingService;

    public function __construct(
        ServiceClonerStateServiceInterface $serviceClonerStateService,
        ServiceClonerCommandLineDumperService $serviceClonerCommandLineDumperService,
        ConfigurationServiceInterface $configurationService,
        ServiceClonerNamingServiceInterface $serviceClonerNamingService
    ) {
        parent::__construct();
        $this->serviceClonerStateService = $serviceClonerStateService;
        $this->configurationService = $configurationService;
        $this->serviceClonerCommandLineDumperService = $serviceClonerCommandLineDumperService;
        $this->serviceClonerNamingService = $serviceClonerNamingService;
    }

    protected function configure(): void
    {
        $this->setName('service:dump:command-line')
            ->addArgument(
                self::ARGUMENT_SERVICE_NAME,
                InputArgument::REQUIRED,
                'Service name'
            )
            ->addArgument(
                self::ARGUMENT_INSTANCE_INDEX,
                InputArgument::REQUIRED,
                'Instance index'
            )
            ->addArgument(
                self::ARGUMENT_REPLICA_FILESYSTEM_PATH,
                InputArgument::REQUIRED,
                'Replica filesystem pmath'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $containerName = $input->getArgument(self::ARGUMENT_SERVICE_NAME);
        $index = (int) $input->getArgument(self::ARGUMENT_INSTANCE_INDEX);
        $path = $input->getArgument(self::ARGUMENT_REPLICA_FILESYSTEM_PATH);

        $output->writeln(sprintf('<info>Container Name</info> %s', $containerName));
        $output->writeln(sprintf('<info>Index</info> %s', $index));
        $output->writeln(sprintf('<info>Path</info> %s', $path));
        $output->writeln('<info>-------------------</info>');
        $output->writeln($this->serviceClonerCommandLineDumperService->dump(new ContainerParameterDTO(
            $containerName,
            $index,
            $path,
        )));

        return 0;
    }

    /**
     * @param $output
     * @param $a
     */
    protected function getWrite($output, $a): void
    {
        $output->write(Yaml::dump(
            $a,
            2,
            2,
            Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_NULL_AS_TILDE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
        ));
    }
}
