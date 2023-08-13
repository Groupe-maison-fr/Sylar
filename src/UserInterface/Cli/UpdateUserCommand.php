<?php

declare(strict_types=1);

namespace App\UserInterface\Cli;

use App\Infrastructure\Security\Models\User;
use App\Infrastructure\Security\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('user:update', description: 'Update a user')]
final class UpdateUserCommand extends Command
{
    private const ARGUMENT_USERNAME = 'username';
    private const ARGUMENT_PASSWORD = 'password';
    private const OPTION_ROLES = 'roles';

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
            )
            ->addArgument(
                self::ARGUMENT_PASSWORD,
                InputArgument::REQUIRED,
                'password',
            )
            ->addOption(
                self::OPTION_ROLES,
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'roles',
                ['ROLE_USER'],
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User(
            $input->getArgument(self::ARGUMENT_USERNAME),
            $input->getOption(self::OPTION_ROLES),
        );
        $user->setPassword($input->getArgument(self::ARGUMENT_PASSWORD));

        try {
            $this->userService->updateUser($user);
        } catch (Throwable $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
