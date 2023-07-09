<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use Docker\API\Endpoint\ContainerList;
use Docker\API\Model\ContainerSummaryItem;
use Docker\API\Model\Port;
use Docker\Docker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('docker:list', description: 'List running dockers')]
final class ListDockerCommand extends Command
{
    public function __construct(
        #[Autowire('@docker.readwrite')]
        private Docker $docker,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Name', 'Image', 'Ports', 'Labels'])
            ->setRows(
                array_map(
                    fn (ContainerSummaryItem $container) => [
                        implode(',', $container->getNames()),
                        $container->getImage(),
                        implode(PHP_EOL, array_map(
                            fn (Port $port) => $port->getPublicPort() . ':' . $port->getPrivatePort(),
                            $container->getPorts(),
                        )),
                        implode(PHP_EOL, array_map(
                            fn (string $value, string $key) => $key . '=' . $value,
                            iterator_to_array($container->getLabels()),
                            array_keys(iterator_to_array($container->getLabels())),
                        )),
                    ],
                    $this->docker->executeEndpoint(new ContainerList([])),
                ),
            );
        $table->render();

        return Command::SUCCESS;
    }
}
