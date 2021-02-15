<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use fXmlRpc\Client as fXmlRpcClient;
use fXmlRpc\Transport\HttpAdapterTransport;
use GuzzleHttp\Client as GuzzleHttpClient;
use Http\Adapter\Guzzle6\Client as HttpAdapterGuzzle6Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Supervisor\Supervisor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RestartSupervisordWorkersCommand extends Command
{
    public const TOOLS_RESTART_WORKER_COMMAND = 'tools:restart-worker';

    private Supervisor $supervisor;

    public function __construct(
        string $supervisordUrl,
        string $supervisordUser,
        string $supervisordPassword
    ) {
        parent::__construct();
        $this->supervisor = new Supervisor(new fXmlRpcClient(
            $supervisordUrl,
            new HttpAdapterTransport(
                new GuzzleMessageFactory(),
                new HttpAdapterGuzzle6Client(new GuzzleHttpClient([
                    'auth' => [$supervisordUser, $supervisordPassword],
                ]))
            )));
    }

    protected function configure(): void
    {
        $this->setName(self::TOOLS_RESTART_WORKER_COMMAND)
            ->setDescription('restart supervisord process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->supervisor->stopProcessGroup('php-worker', true);
        $this->supervisor->startProcessGroup('php-worker', true);

        return 0;
    }
}
