<?php

declare(strict_types=1);
declare(ticks=1);

namespace App\Infrastructure\Docker;

use App\Core\ServiceCloner\Configuration\Object\Environment;
use App\Core\ServiceCloner\Configuration\Object\Label;
use App\Core\ServiceCloner\Configuration\Object\Mount;
use App\Core\ServiceCloner\Configuration\Object\Port;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Infrastructure\Docker\ContainerParameter\ContainerParameterDTO;
use App\Infrastructure\Docker\ContainerParameter\EnvironmentFactoryInterface;
use App\Infrastructure\Docker\ContainerParameter\LabelFactoryInterface;
use App\Infrastructure\Docker\ContainerParameter\MountFactoryInterface;
use App\Infrastructure\Docker\ContainerParameter\PortBindingFactoryInterface;
use ArrayObject;
use Docker\API\Exception\ContainerCreateBadRequestException;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersCreatePostResponse201;
use Docker\API\Model\HostConfig;
use Docker\Docker;
use Exception;
use Psr\Log\LoggerInterface;

final class ContainerCreationService implements ContainerCreationServiceInterface
{
    private LoggerInterface $logger;
    private Docker $docker;
    private PortBindingFactoryInterface $bindingSpecificationFactory;
    private MountFactoryInterface $mountSpecificationFactory;
    private EnvironmentFactoryInterface $environmentSpecificationFactory;
    private LabelFactoryInterface $labelSpecificationFactory;
    private ContainerImageServiceInterface $dockerPullService;

    public function __construct(
        Docker $docker,
        LoggerInterface $logger,
        PortBindingFactoryInterface $portBindingSpecificationFactory,
        MountFactoryInterface $mountSpecificationFactory,
        EnvironmentFactoryInterface $environmentSpecificationFactory,
        LabelFactoryInterface $labelSpecificationFactory,
        ContainerImageServiceInterface $dockerPullService
    ) {
        $this->docker = $docker;
        $this->logger = $logger;
        $this->bindingSpecificationFactory = $portBindingSpecificationFactory;
        $this->mountSpecificationFactory = $mountSpecificationFactory;
        $this->environmentSpecificationFactory = $environmentSpecificationFactory;
        $this->labelSpecificationFactory = $labelSpecificationFactory;
        $this->dockerPullService = $dockerPullService;
    }

    public function createDocker(
        ContainerParameterDTO $containerParameter,
        Service $service
    ): void {
        if (!$this->dockerPullService->imageExists($service->getImage())) {
            $this->dockerPullService->pullImage($service->getImage());
        }
        $hostConfig = new HostConfig();

        $this->setMounts($containerParameter, $hostConfig, $service);
        $this->setPortBindings($containerParameter, $hostConfig, $service);

        $container = new ContainersCreatePostBody();
        $container->setImage($service->getImage());
        $container->setHostConfig($hostConfig);
        $this->setLabel($containerParameter, $container, $service);
        $this->setEnv($containerParameter, $container, $service);

        try {
            /** @var ContainersCreatePostResponse201 $containerCreate */
            $containerCreate = $this->docker->containerCreate($container, ['name' => $containerParameter->getName()]);
            $this->docker->containerStart($containerCreate->getId());
        } catch (ContainerCreateBadRequestException $exception) {
            $this->logger->error(sprintf('createDocker: %s %s', $exception->getMessage(), $exception->getErrorResponse()->getMessage()));
        } catch (Exception $exception) {
            $this->logger->error(sprintf('createDocker: %s', $exception->getMessage()));
            throw $exception;
        }
    }

    private function setMounts(ContainerParameterDTO $containerParameter, HostConfig $hostConfig, Service $service): void
    {
        if ($service->getMounts()->isEmpty()) {
            return;
        }
        $hostConfig->setMounts($service->getMounts()->map(function (Mount $mount) use ($containerParameter) {
            return $this->mountSpecificationFactory->createFromConfiguration($containerParameter, $mount);
        })->toArray());
    }

    private function setPortBindings(ContainerParameterDTO $containerParameter, HostConfig $hostConfig, service $service): void
    {
        if ($service->getPorts()->isEmpty()) {
            return;
        }
        $hostConfig->setPortBindings(new ArrayObject(array_reduce(
            $service->getPorts()->toArray(),
            function (array $previous, Port $port) use ($containerParameter) {
                $portBinding = $this->bindingSpecificationFactory->createFromConfiguration($containerParameter, $port);
                if (!isset($previous[$port->getContainerPort()])) {
                    $previous[$port->getContainerPort()] = [];
                }
                array_push($previous[$port->getContainerPort()], $portBinding);

                return $previous;
            },
            []
        )));
    }

    private function setEnv(ContainerParameterDTO $containerParameter, ContainersCreatePostBody $container, Service $service): void
    {
        if ($service->getEnvironments()->isEmpty()) {
            return;
        }
        $container->setEnv($service->getEnvironments()->map(function (Environment $environment) use ($containerParameter) {
            return $this->environmentSpecificationFactory->createFromConfiguration($containerParameter, $environment);
        })->toArray());
    }

    private function setLabel(ContainerParameterDTO $containerParameter, ContainersCreatePostBody $container, Service $service): void
    {
        if ($service->getLabels()->isEmpty()) {
            return;
        }
        $container->setLabels(
            new ArrayObject(array_reduce($service->getLabels()->toArray(),
                function (array $labels, Label $label) use ($containerParameter) {
                    list($key, $value) = $this->labelSpecificationFactory->createFromConfiguration($containerParameter, $label);
                    $labels[$key] = $value;

                    return $labels;
                }, []
            ))
        );
    }
}
