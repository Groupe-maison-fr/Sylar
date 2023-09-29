<?php

declare(strict_types=1);

namespace App\Common\Tests;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

final class BufferedLoggerHandler extends AbstractProcessingHandler
{
    private array $logs = [];

    public function __construct(bool $bubble = true)
    {
        parent::__construct(Logger::DEBUG, $bubble);
        $this->reset();
    }

    public function reset(): void
    {
        $this->logs = [];
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function isHandling(LogRecord $record): bool
    {
        return parent::isHandling($record);
    }

    public function handle(LogRecord $record): bool
    {
        return parent::handle($record);
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }

    protected function write(LogRecord $record): void
    {
        $this->logs[] = $record;
    }
}
