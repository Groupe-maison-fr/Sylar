<?php

declare(strict_types=1);

namespace App\Infrastructure\PostContainerDumpActions;

use App\Infrastructure\Process\Process;
use App\UserInterface\Cli\RestartSupervisordWorkersCommand;

final class RestartSupervisorMessengerConsumer implements PostContainerDumpServiceInterface
{
    private Process $process;

    public function __construct(
        Process $process
    ) {
        $this->process = $process;
    }

    public function execute(): void
    {
        $this->process->run('bin/console', RestartSupervisordWorkersCommand::TOOLS_RESTART_WORKER_COMMAND);
    }
}
