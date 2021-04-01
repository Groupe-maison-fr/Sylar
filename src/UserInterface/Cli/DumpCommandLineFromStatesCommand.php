<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\ServiceClonerCommandLineDumperService;
use App\Core\ServiceCloner\ServiceClonerNamingServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class DumpCommandLineFromStatesCommand extends Command
{
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
        $this->setName('service:dump:command-line-from-state');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->serviceClonerStateService->getStates() as $serviceClonerStatusDTO) {
            $containerName = $this->serviceClonerNamingService->getDockerName($serviceClonerStatusDTO->getMasterName(), $serviceClonerStatusDTO->getInstanceName());
            $path = $this->serviceClonerNamingService->getZfsFilesystemPath($serviceClonerStatusDTO->getMasterName(), $serviceClonerStatusDTO->getInstanceName());
            $output->writeln(sprintf('<info>Container Name</info> %s', $containerName));
            $output->writeln(sprintf('<info>Index</info> %s', $serviceClonerStatusDTO->getIndex()));
            $output->writeln(sprintf('<info>Path</info> %s', $path));
            $output->writeln('<info>-------------------</info>');
            $output->writeln($this->serviceClonerCommandLineDumperService->dump(new ContainerParameterDTO(
                $containerName,
                $serviceClonerStatusDTO->getIndex(),
                $path,
            )));
            $output->writeln('');
        }

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
