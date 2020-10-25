<?php
declare(strict_types=1);

namespace App\Common\Tests;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class BufferedLoggerHandler extends AbstractProcessingHandler
{
    private $logs = [];

    public function __construct(bool $bubble = true)
    {
        parent::__construct(Logger::DEBUG, $bubble);
        $this->reset();
    }

    public function reset()
    {
        $this->logs = [];
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function isHandling(array $record): bool
    {
        return parent::isHandling($record);
    }

    public function handle(array $record): bool
    {
        return parent::handle($record);
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }

    protected function write(array $record): void
    {
        $this->logs[]=$record;
    }
}
