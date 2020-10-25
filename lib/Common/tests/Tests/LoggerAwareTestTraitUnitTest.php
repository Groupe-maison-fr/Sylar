<?php
declare(strict_types=1);

namespace Tests\Common\Tests;

use App\Common\Tests\LoggerAwareTestTrait;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Bridge\Monolog\Logger;

final class LoggerAwareTestTraitUnitTest extends TestCase
{
    use LoggerAwareTestTrait;

    /**
     * @var Logger
     */
    private $logger;

    public function setUp():void
    {
        $this->logger = new Logger('base');
        $this->initLoggerBufferedHandler($this->logger);
    }

    /**
     * @test
     */
    public function it_should_store_all_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');

        $this->assertCount(3,$this->getBufferedLogs());
        $this->resetBufferedLoggerHandler();

        $this->logger->notice('test_notice1');
        $this->assertCount(1,$this->getBufferedLogs());
    }

    /**
     * @test
     */
    public function it_should_found_string_in_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        $this->assertContainsLogWithSameMessage('test_info');
        $this->assertContainsLogWithSameMessage('test_error');
        $this->assertContainsLogWithSameMessage('test_notice');
        $this->assertNotContainsLogWithSameMessage('not in log');
    }

    /**
     * @test
     */
    public function it_should_not_found_string_in_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        $this->assertNotContainsLogWithSameMessage('not in log');
    }

    /**
     * @test
     */
    public function it_should_raise_error_when_does_not_found_string_in_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        self::expectException(ExpectationFailedException::class);
        $this->assertContainsLogWithSameMessage('info');
    }

    /**
     * @test
     */
    public function it_should_not_not_found_string_in_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        self::expectException(ExpectationFailedException::class);
        $this->assertNotContainsLogWithSameMessage('test_info');
    }

    /**
     * @test
     */
    public function it_should_found_substring_in_logs(){
        $this->logger->info('a test_info in logs');
        $this->logger->error('a test_error in logs');
        $this->logger->notice('a test_notice in logs');
        $this->assertContainsLogThatMatchRegularExpression('!test_info!');
        $this->assertContainsLogThatMatchRegularExpression('!test_error!');
        $this->assertContainsLogThatMatchRegularExpression('!test_notice!');
        $this->assertContainsLogThatMatchRegularExpression('!^a test_notice!');
        $this->assertContainsLogThatMatchRegularExpression('!test_NoTiCe!i');
    }

    /**
     * @test
     */
    public function it_should_not_found_substring_in_logs(){
        $this->logger->info('a test_info in logs');
        $this->logger->error('a test_error in logs');
        $this->logger->notice('a test_notice in logs');
        self::expectException(ExpectationFailedException::class);
        $this->assertNotContainsLogThatMatchRegularExpression('!test_info!');
    }

    /**
     * @test
     */
    public function it_should_raise_error_when_does_not_found_substring_in_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        self::expectException(ExpectationFailedException::class);
        $this->assertContainsLogThatMatchRegularExpression('!not(.*)in string!');
    }

    /**
     * @test
     */
    public function it_should_not_raise_error_when_does_not_found_substring_in_logs(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        $this->assertNotContainsLogThatMatchRegularExpression('!not(.*)in string!');
    }

    /**
     * @test
     */
    public function it_should_found_in_logs_when_using_callable(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        $this->assertContainsLog(function(array $logElement){
            return $logElement['level_name']===strtoupper(LogLevel::ERROR);
        });

        self::expectException(ExpectationFailedException::class);
        $this->assertContainsLog(function(array $logElement){
            return $logElement['level_name']===strtoupper(LogLevel::WARNING);
        });
    }

    /**
     * @test
     */
    public function it_should_not_found_in_logs_when_using_callable(){
        $this->logger->info('test_info');
        $this->logger->error('test_error');
        $this->logger->notice('test_notice');
        $this->assertNotContainsLog(function(array $logElement){
            return $logElement['level_name']===strtoupper(LogLevel::WARNING);
        });
    }

}
