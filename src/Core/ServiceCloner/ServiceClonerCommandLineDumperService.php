<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Environment;
use App\Core\ServiceCloner\Configuration\Object\Label;
use App\Core\ServiceCloner\Configuration\Object\LifeCycleHooks;
use App\Core\ServiceCloner\Configuration\Object\Mount;
use App\Core\ServiceCloner\Configuration\Object\Port;
use App\Core\ServiceCloner\Configuration\Object\PostDestroyCommand;
use App\Core\ServiceCloner\Configuration\Object\PostStartCommand;
use App\Core\ServiceCloner\Configuration\Object\PostStartWaiter;
use App\Core\ServiceCloner\Configuration\Object\PreStartCommand;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use App\Infrastructure\Docker\ContainerParameter\ConfigurationExpressionGeneratorInterface;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use Doctrine\Common\Collections\Collection;
use DomainException;

final class ServiceClonerCommandLineDumperService
{
    public function __construct(
        private ConfigurationServiceInterface $dockerConfiguration,
        private ConfigurationExpressionGeneratorInterface $configurationExpressionGenerator,
    ) {
    }

    public function dump(ContainerParameterDTO $container): string
    {
        return $this->dumpNode($container, $this->dockerConfiguration->getConfiguration());
    }

    private function evaluate(ContainerParameterDTO $container, string $expression): string
    {
        return $this->configurationExpressionGenerator->generate($container, $expression);
    }

    private function dumpNode(ContainerParameterDTO $container, mixed $node): ?string
    {
        switch (true) {
            case is_array($node):
                return implode(' ', array_map(fn ($item) => $this->dumpNode($container, $item), $node));
            case $node instanceof Collection:
                return implode(' ', $node->map(fn ($item) => $this->dumpNode($container, $item))->toArray());
            case $node instanceof PreStartCommand:
            case $node instanceof PostStartWaiter:
            case $node instanceof PostStartCommand:
            case $node instanceof PostDestroyCommand:
            case $node instanceof LifeCycleHooks:
                return null;
            case $node instanceof Environment:
                return sprintf(
                    '--env %s=%s',
                    $this->evaluate($container, $node->name),
                    $this->evaluate($container, $this->evaluate($container, $node->value)),
                );
            case $node instanceof Label:
                return sprintf(
                    '--label %s=%s',
                    $this->evaluate($container, $node->name),
                    $this->evaluate($container, $node->value),
                );
            case $node instanceof Mount:
                return sprintf(
                    '--mount type=bind,target=%s,source=%s',
                    $this->evaluate($container, $node->target),
                    $this->evaluate($container, $node->source),
                );
            case $node instanceof Port:
                return sprintf(
                    '--publish %s:%s:%s',
                    $this->evaluate($container, $node->hostIp),
                    str_replace(['/tcp', '/udp'], '', $this->evaluate($container, $node->hostPort)),
                    $this->evaluate($container, $node->containerPort),
                );
            case $node instanceof Service:
                $networkMode = $node->networkMode === null ? '' : $this->evaluate($container, $node->networkMode);

                return implode(' ', array_filter([
                    'docker run',
                    $this->dumpNode($container, $node->environments),
                    $this->dumpNode($container, $node->mounts),
                    $this->dumpNode($container, $node->ports),
                    $this->dumpNode($container, $node->labels),
                    $node->networkMode !== null ? sprintf('--net=%s', $networkMode) : '',
                    '--name ' . $this->evaluate($container, $container->name),
                    '--detach',
                    $this->evaluate($container, $node->image),
                    $node->entryPoint != '' ? sprintf('--entrypoint %s', $this->evaluate($container, $node->entryPoint)) : '',
                    $this->evaluate($container, $node->command),
                ]));
            case $node instanceof ServiceCloner:
                return $this->dumpNode($container, $node->services);
        }
        throw new DomainException('Unknown type to map');
    }
}
