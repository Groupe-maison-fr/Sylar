<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\Object\PostDestroyCommand;
use App\Core\ServiceCloner\Configuration\Object\PostStartCommand;
use App\Core\ServiceCloner\Configuration\Object\PostStartWaiter;
use App\Core\ServiceCloner\Configuration\Object\PreStartCommand;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\Configuration\TreeBuilderConfiguration;
use App\Infrastructure\Docker\ContainerExecServiceInterface;
use App\Infrastructure\Docker\ContainerParameter\ConfigurationExpressionGeneratorInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\ContainerWaitUntilLogServiceInterface;
use App\Infrastructure\Process\SudoProcess;
use Doctrine\Common\Collections\ArrayCollection;

final class ServiceClonerLifeCycleHookService implements ServiceClonerLifeCycleHookServiceInterface
{
    private ContainerWaitUntilLogServiceInterface $dockerWaitUntilLogService;

    private ContainerExecServiceInterface $containerExecService;

    private SudoProcess $process;

    private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator;

    public function __construct(
        ContainerWaitUntilLogServiceInterface $dockerWaitUntilLogService,
        ContainerExecServiceInterface $containerExecService,
        SudoProcess $process,
        ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator
    ) {
        $this->dockerWaitUntilLogService = $dockerWaitUntilLogService;
        $this->containerExecService = $containerExecService;
        $this->process = $process;
        $this->configurationExpressionGenerator = $configurationExpressionGenerator;
    }

    private function processArray(ContainerParameterDTO $containerParameter, ArrayCollection $arguments): array
    {
        return $arguments->map(function (string $argument) use ($containerParameter) {
            return $this->configurationExpressionGenerator->generate($containerParameter, $argument);
        })->toArray();
    }

    public function preStart(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        $dockerConfiguration->getLifeCycleHooks()->getPreStartCommands()->map(function (PreStartCommand $command) use ($containerParameter): void {
            $arguments = $this->processArray($containerParameter, $command->getCommand());
            if ($command->getExecutionEnvironment() === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_HOST) {
                $this->process->run(...$arguments);

                return;
            }
            if ($command->getExecutionEnvironment() === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_CLONE_CONTAINER && $containerParameter->getIndex() !== 0) {
                $this->containerExecService->exec($containerParameter->getName(), ...$arguments);

                return;
            }
        });
    }

    public function postStartWaiter(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        $dockerConfiguration->getLifeCycleHooks()->getPostStartWaiters()->map(function (PostStartWaiter $waiter) use ($containerParameter): void {
            if ($waiter->getType() === TreeBuilderConfiguration::POST_START_WAITER_LOG_MATCH) {
                $this->dockerWaitUntilLogService->wait($containerParameter, $waiter->getExpression(), $waiter->getTimeout());
            }
        });
    }

    public function postStart(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        $dockerConfiguration->getLifeCycleHooks()->getPostStartCommands()->map(function (PostStartCommand $command) use ($containerParameter): void {
            $arguments = $this->processArray($containerParameter, $command->getCommand());
            if ($command->getExecutionEnvironment() === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_HOST) {
                $this->process->run(...$arguments);

                return;
            }
            if ($command->getExecutionEnvironment() === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_CLONE_CONTAINER) {
                $this->containerExecService->exec($containerParameter->getName(), ...$arguments);

                return;
            }
        });
    }

    public function postDestroy(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        $dockerConfiguration->getLifeCycleHooks()->getPostDestroyCommands()->map(function (PostDestroyCommand $command) use ($containerParameter): void {
            $arguments = $this->processArray($containerParameter, $command->getCommand());
            if ($command->getExecutionEnvironment() === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_HOST) {
                $this->process->run(...$arguments);

                return;
            }
        });
    }
}
