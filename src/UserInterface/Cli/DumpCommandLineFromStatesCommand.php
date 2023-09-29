<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\ServiceClonerCommandLineDumperService;
use App\Core\ServiceCloner\ServiceClonerNamingServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('service:dump:command-line-from-state')]
final class DumpCommandLineFromStatesCommand extends Command
{
    public function __construct(
        private ServiceClonerStateServiceInterface $serviceClonerStateService,
        private ServiceClonerCommandLineDumperService $serviceClonerCommandLineDumperService,
        private ServiceClonerNamingServiceInterface $serviceClonerNamingService,
    ) {
        parent::__construct();
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
}
