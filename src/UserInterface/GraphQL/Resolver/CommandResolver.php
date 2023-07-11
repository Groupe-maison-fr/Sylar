<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\CommandExecutor\CommandExecutorInterface;
use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Command;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class CommandResolver implements QueryInterface
{
    public function __construct(
        private ConfigurationServiceInterface $configurationService,
        private CommandExecutorInterface $commandExecutor,
    ) {
    }

    public function __invoke(ResolveInfo $info, Command $command, Argument $args): mixed
    {
        switch ($info->fieldName) {
            case 'subCommands':
                return $command->getSubCommands();
            case 'name':
                return $command->getName();
            case 'output':
                return $this->commandExecutor->run($command);
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    public function resolveByName(string $commandName): Command
    {
        return $this->configurationService->getConfiguration()->getCommandByName($commandName);
    }

    /**
     * @return ArrayCollection<int, Command>
     */
    public function resolve(): Collection
    {
        return $this->configurationService->getConfiguration()->getCommands();
    }
}
