<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\Stream;

use Docker\Stream\DockerRawStream;

final class DockerRawStreamUntil extends DockerRawStream
{
    private bool $shouldExit = false;

    public function exitWait(): void
    {
        $this->shouldExit = true;
    }

    public function wait(): void
    {
        while (!$this->shouldExit && !$this->stream->eof()) {
            $this->readFrame();
        }
    }
}
