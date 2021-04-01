<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\CommandExecutor\CommandExecutorInterface;
use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Command;
use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class CommandResolver implements ResolverInterface
{
    private ConfigurationServiceInterface $configurationService;

    private CommandExecutorInterface $commandExecutor;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        CommandExecutorInterface $commandExecutor
    ) {
        $this->configurationService = $configurationService;
        $this->commandExecutor = $commandExecutor;
    }

    public function __invoke(ResolveInfo $info, Command $command, Argument $args)
    {
        switch ($info->fieldName) {
            case 'subCommands':
                return $command->getSubCommands();
            case 'name':
                return $command->getName();
            case 'output':
                return $this->commandExecutor->run($command);
        }
    }

    public function resolveByName(string $commandName): Command
    {
        return $this->configurationService->getConfiguration()->getCommandByName($commandName);
    }

    public function resolve(): Collection
    {
        return $this->configurationService->getConfiguration()->getCommands();
    }
}
