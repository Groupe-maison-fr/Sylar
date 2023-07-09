<?php

declare(strict_types=1);

namespace App\Infrastructure\PostContainerDumpActions;

use App\Infrastructure\Process\Process;
use App\UserInterface\Cli\RestartSupervisordWorkersCommand;

final class RestartSupervisorMessengerConsumer implements PostContainerDumpServiceInterface
{
    public function __construct(
        private Process $process,
    ) {
    }

    public function execute(): void
    {
        // $this->process->run('bin/console', RestartSupervisordWorkersCommand::TOOLS_RESTART_WORKER_COMMAND);
    }
}
