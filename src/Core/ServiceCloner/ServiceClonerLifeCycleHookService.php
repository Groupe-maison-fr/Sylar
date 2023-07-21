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
use App\Infrastructure\Process\ProcessInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class ServiceClonerLifeCycleHookService implements ServiceClonerLifeCycleHookServiceInterface
{
    public function __construct(
        private ContainerWaitUntilLogServiceInterface $dockerWaitUntilLogService,
        private ContainerExecServiceInterface $containerExecService,
        private ProcessInterface $process,
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    /**
     * @param string[] $arguments
     *
     * @return string[]
     */
    private function processArray(ContainerParameterDTO $containerParameter, array $arguments): array
    {
        return array_map(
            fn (string $argument) => $this->configurationExpressionGenerator->generate($containerParameter, $argument),
            $arguments,
        );
    }

    public function preStart(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        if ($dockerConfiguration->lifeCycleHooks === null) {
            return;
        }
        (new ArrayCollection($dockerConfiguration->lifeCycleHooks->preStartCommands))->map(function (PreStartCommand $command) use ($containerParameter): void {
            $arguments = $this->processArray($containerParameter, $command->command);
            if ($command->executionEnvironment === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_HOST) {
                $this->process->run(...$arguments);

                return;
            }
            if ($command->executionEnvironment === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_CLONE_CONTAINER && $containerParameter->index !== 0) {
                $this->containerExecService->exec($containerParameter->name, ...$arguments);

                return;
            }
        });
    }

    public function postStartWaiter(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        if ($dockerConfiguration->lifeCycleHooks === null) {
            return;
        }
        (new ArrayCollection($dockerConfiguration->lifeCycleHooks->postStartWaiters))->map(function (PostStartWaiter $waiter) use ($containerParameter): void {
            if ($waiter->type === TreeBuilderConfiguration::POST_START_WAITER_LOG_MATCH) {
                $this->dockerWaitUntilLogService->wait($containerParameter, $waiter->expression, $waiter->timeout);
            }
        });
    }

    public function postStart(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        if ($dockerConfiguration->lifeCycleHooks === null) {
            return;
        }
        (new ArrayCollection($dockerConfiguration->lifeCycleHooks->postStartCommands))->map(function (PostStartCommand $command) use ($containerParameter): void {
            $arguments = $this->processArray($containerParameter, $command->command);
            if ($command->executionEnvironment === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_HOST) {
                $this->process->run(...$arguments);

                return;
            }
            if ($command->executionEnvironment === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_CLONE_CONTAINER) {
                $this->containerExecService->exec($containerParameter->name, ...$arguments);

                return;
            }
        });
    }

    public function postDestroy(Service $dockerConfiguration, ContainerParameterDTO $containerParameter): void
    {
        if ($dockerConfiguration->lifeCycleHooks === null) {
            return;
        }
        (new ArrayCollection($dockerConfiguration->lifeCycleHooks->postDestroyCommands))->map(function (PostDestroyCommand $command) use ($containerParameter): void {
            $arguments = $this->processArray($containerParameter, $command->command);
            if ($command->executionEnvironment === TreeBuilderConfiguration::EXECUTION_ENVIRONMENT_HOST) {
                $this->process->run(...$arguments);

                return;
            }
        });
    }
}
