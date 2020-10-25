<?php
declare(strict_types=1);

namespace App\Common\Tests;

use Symfony\Bridge\Monolog\Logger;

trait LoggerAwareTestTrait
{
    /**
     * @var BufferedLoggerHandler
     */
    private $bufferedLoggerHandler;

    public function getBufferedLogs(): array
    {
        return $this->bufferedLoggerHandler->getLogs();
    }

    public function resetBufferedLoggerHandler(): void
    {
        $this->bufferedLoggerHandler->reset();
    }

    protected function initLoggerBufferedHandler(Logger $logger): void
    {
        $this->bufferedLoggerHandler = new BufferedLoggerHandler();
        $logger->pushHandler($this->bufferedLoggerHandler);
    }

    protected function assertContainsLog(callable $test, string $message=''){
        self::assertNotEmpty(array_filter($this->bufferedLoggerHandler->getLogs(),$test),$message);
    }

    protected function assertNotContainsLog(callable $test, string $message=''){
        self::assertEmpty(array_filter($this->bufferedLoggerHandler->getLogs(),$test),$message);
    }

    protected function assertContainsLogWithSameMessage(string $needle, string $message = '')
    {
        $this->assertContainsLog(function (array $logElement) use ($needle) {
            return $logElement['message'] === $needle;
        }, $message);
    }

    protected function assertContainsLogThatMatchRegularExpression(string $regexp, string $message = '')
    {
        $this->assertContainsLog(function (array $logElement) use ($regexp) {
            return preg_match($regexp,$logElement['message']);
        }, $message);
    }

    protected function assertNotContainsLogWithSameMessage(string $needle, string $message = '')
    {
        $this->assertNotContainsLog(function (array $logElement) use ($needle) {
            return $logElement['message'] === $needle;
        }, $message);
    }

    protected function assertNotContainsLogThatMatchRegularExpression(string $regexp, string $message = '')
    {
        $this->assertNotContainsLog(function (array $logElement) use ($regexp) {
            return preg_match($regexp,$logElement['message']);
        }, $message);
    }

}
