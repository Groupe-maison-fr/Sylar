<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloner;

use App\Core\ServiceCloner\CommandExecutor\CommandExecutor;
use App\Core\ServiceCloner\CommandExecutor\CommandExecutorInterface;
use App\Core\ServiceCloner\Configuration\Object\Command;
use Tests\AbstractIntegrationTestCase;

/**
 * @internal
 */
final class CommandExecutorTest extends AbstractIntegrationTestCase
{
    private CommandExecutorInterface $commandExecutor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandExecutor = $this->getService(CommandExecutor::class);
    }

    /** @test */
    public function it_should_run_commands(): void
    {
        $command = new Command(
            'test',
            [
                'pwd',
                'cd /tmp;pwd',
                'cd /var;pwd',
            ],
        );
        $output = $this->commandExecutor->run($command);
        self::assertCount(3, $output);
        self::assertSame('pwd', $output[0]->getSubCommand());
        self::assertSame(['/tmp'], $output[1]->getOutput());
        self::assertSame(['/var'], $output[2]->getOutput());
    }
}
