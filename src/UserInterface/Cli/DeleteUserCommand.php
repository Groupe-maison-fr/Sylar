<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Infrastructure\Security\Models\User;
use App\Infrastructure\Security\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('user:delete', description: 'Delete a user')]
final class DeleteUserCommand extends Command
{
    private const ARGUMENT_USERNAME = 'username';

    public function __construct(
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_USERNAME,
                InputArgument::REQUIRED,
                'username',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User($input->getArgument(self::ARGUMENT_USERNAME));

        try {
            $this->userService->deleteUser($user);
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
