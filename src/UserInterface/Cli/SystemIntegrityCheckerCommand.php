<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Core\ServiceCloner\UseCase\StartServiceHandler;
use App\Infrastructure\Process\Process;
use App\Infrastructure\Process\ProcessInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('system:checker', description: 'System integrity checker')]
final class SystemIntegrityCheckerCommand extends Command
{
    private MessageBusInterface $messageBus;
    private StartServiceHandler $startServiceHandler;
    private ProcessInterface $process;
    private Process $localProcess;

    public function __construct(
        MessageBusInterface $messageBus,
        StartServiceHandler $startServiceHandler,
        ProcessInterface $process,
        Process $localProcess
    ) {
        parent::__construct();
        $this->messageBus = $messageBus;
        $this->startServiceHandler = $startServiceHandler;
        $this->process = $process;
        $this->localProcess = $localProcess;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('<info>Container sudo execution</info> ');
        $processOutput = $this->localProcess->run('id')->getStdOutput();
        $output->writeln(preg_match('!uid=!', $processOutput) ? '✅' : '❌');
        $output->writeln(trim($processOutput));
        $output->writeln('');

        $output->write('<info>Docker to host command execution</info> ');
        $processOutput = $this->process->run('id')->getStdOutput();
        $output->writeln(preg_match('!uid=!', $processOutput) ? '✅' : '❌');
        $output->writeln(trim($processOutput));
        $output->writeln('');

        $output->writeln('<comment>ok</comment>');

        return 0;
    }
}
