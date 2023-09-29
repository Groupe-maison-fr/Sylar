<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Infrastructure\ServerSideEvent\ServerSideEventPublisherInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

final class ServerSideEventPublisherHandler extends AbstractProcessingHandler
{
    public function __construct(
        private readonly ServerSideEventPublisherInterface $serverSideEventPublisher,
        int|string|Level $level = Level::Info,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * @param LogRecord[] $records
     */
    public function handleBatch(array $records): void
    {
        $messages = [];
        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }

            $messages[] = $this->enrichRecord($record);
        }
        if (count($messages) === 0) {
            return;
        }
        $this->serverSideEventPublisher->publish('sylar', [
            'type' => 'log',
            'action' => 'messages',
            'messages' => $messages,
        ]);
    }

    /**
     * @phpstan-return array{id:string, message: string, context: mixed[], level: value-of<Level::VALUES>, level_name: value-of<Level::NAMES>, channel: string, datetime: \DateTimeImmutable, extra: mixed[]}
     */
    private function enrichRecord(LogRecord $record): array
    {
        $_record = $this->processRecord($record)->toArray();
        $_record['id'] = uniqid();

        return $_record;
    }

    protected function write(LogRecord $record): void
    {
        if (!$this->isHandling($record)) {
            return;
        }
        $this->serverSideEventPublisher->publish('sylar', [
            'type' => 'log',
            'action' => 'message',
            'data' => $this->enrichRecord($record),
        ]);
    }
}
