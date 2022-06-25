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
use App\Infrastructure\Docker\ContainerParameter\StringParameterFactoryInterface;
use ArrayObject;
use Docker\API\Exception\ContainerCreateBadRequestException;
use Docker\API\Exception\ContainerCreateInternalServerErrorException;
use Docker\API\Exception\ContainerStartNotFoundException;
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

    private StringParameterFactoryInterface $stringParameterFactory;

    public function __construct(
        Docker $dockerReadWrite,
        LoggerInterface $logger,
        PortBindingFactoryInterface $portBindingSpecificationFactory,
        MountFactoryInterface $mountSpecificationFactory,
        EnvironmentFactoryInterface $environmentSpecificationFactory,
        LabelFactoryInterface $labelSpecificationFactory,
        ContainerImageServiceInterface $dockerPullService,
        StringParameterFactoryInterface $stringParameterFactory
    ) {
        $this->docker = $dockerReadWrite;
        $this->logger = $logger;
        $this->bindingSpecificationFactory = $portBindingSpecificationFactory;
        $this->mountSpecificationFactory = $mountSpecificationFactory;
        $this->environmentSpecificationFactory = $environmentSpecificationFactory;
        $this->labelSpecificationFactory = $labelSpecificationFactory;
        $this->dockerPullService = $dockerPullService;
        $this->stringParameterFactory = $stringParameterFactory;
    }

    public function createDocker(
        ContainerParameterDTO $containerParameter,
        Service $service,
        array $labels
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
        $this->setLabel($containerParameter, $container, $service, $labels);
        $this->setEnv($containerParameter, $container, $service);
        $this->setNetworkMode($containerParameter, $hostConfig, $service);

        try {
            /** @var ContainersCreatePostResponse201 $containerCreate */
            $containerCreate = $this->docker->containerCreate($container, ['name' => $containerParameter->getName()]);
            $this->docker->containerStart($containerCreate->getId());
        } catch (ContainerCreateBadRequestException $exception) {
            $this->logger->error(sprintf('createDocker: %s %s', $exception->getMessage(), $exception->getErrorResponse()->getMessage()));
        } catch (ContainerStartNotFoundException $exception) {
            $this->logger->error(sprintf('createDocker start failure: %s %s', $exception->getMessage(), $exception->getErrorResponse()->getMessage()));
        } catch (ContainerCreateInternalServerErrorException $exception) {
            $this->logger->error(sprintf('createDocker internal error: %s %s', $exception->getMessage(), $exception->getErrorResponse()->getMessage()));
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

    private function setNetworkMode(ContainerParameterDTO $containerParameter, HostConfig $hostConfig, service $service): void
    {
        if ($service->getNetworkMode() === null) {
            return;
        }
        $hostConfig->setNetworkMode($this->stringParameterFactory->createFromConfiguration($containerParameter, $service->getNetworkMode()));
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

    private function setLabel(ContainerParameterDTO $containerParameter, ContainersCreatePostBody $container, Service $service, array $labels): void
    {
        if ($service->getLabels()->isEmpty() && empty($labels)) {
            return;
        }
        $containerLabels = new ArrayObject(array_reduce($service->getLabels()->toArray(),
            function (array $labels, Label $label) use ($containerParameter) {
                [$key, $value] = $this->labelSpecificationFactory->createFromConfiguration($containerParameter, $label);
                $labels[$key] = $value;

                return $labels;
            }, $labels
        ));
        $container->setLabels($containerLabels);
    }
}
