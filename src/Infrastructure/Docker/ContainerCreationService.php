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
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Psr\Log\LoggerInterface;

final class ContainerCreationService implements ContainerCreationServiceInterface
{
    public function __construct(
        private Docker $dockerReadWrite,
        private LoggerInterface $logger,
        private PortBindingFactoryInterface $bindingSpecificationFactory,
        private MountFactoryInterface $mountSpecificationFactory,
        private EnvironmentFactoryInterface $environmentSpecificationFactory,
        private LabelFactoryInterface $labelSpecificationFactory,
        private ContainerImageServiceInterface $dockerPullService,
        private StringParameterFactoryInterface $stringParameterFactory,
    ) {
    }

    public function createDocker(
        ContainerParameterDTO $containerParameter,
        Service $service,
        array $labels,
    ): void {
        if (!$this->dockerPullService->imageExists($service->image)) {
            $this->dockerPullService->pullImage($service->image);
        }
        $hostConfig = new HostConfig();

        $this->setMounts($containerParameter, $hostConfig, $service);
        $this->setPortBindings($containerParameter, $hostConfig, $service);

        $container = new ContainersCreatePostBody();
        $container->setImage($service->image);
        $container->setHostConfig($hostConfig);
        $this->setLabel($containerParameter, $container, $service, $labels);
        $this->setEnv($containerParameter, $container, $service);
        $this->setNetworkMode($containerParameter, $hostConfig, $service);

        try {
            /** @var ContainersCreatePostResponse201 $containerCreate */
            $containerCreate = $this->dockerReadWrite->containerCreate($container, ['name' => $containerParameter->name]);
            $this->dockerReadWrite->containerStart($containerCreate->getId());
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
        $mounts = new ArrayCollection($service->mounts);
        if ($mounts->isEmpty()) {
            return;
        }
        $hostConfig->setMounts($mounts->map(fn (Mount $mount) => $this->mountSpecificationFactory->createFromConfiguration($containerParameter, $mount))->toArray());
    }

    private function setPortBindings(ContainerParameterDTO $containerParameter, HostConfig $hostConfig, service $service): void
    {
        $ports = new ArrayCollection($service->ports);
        if ($ports->isEmpty()) {
            return;
        }
        $hostConfig->setPortBindings(new ArrayObject(array_reduce(
            $ports->toArray(),
            function (array $previous, Port $port) use ($containerParameter) {
                $portBinding = $this->bindingSpecificationFactory->createFromConfiguration($containerParameter, $port);
                if (!isset($previous[$port->containerPort])) {
                    $previous[$port->containerPort] = [];
                }
                array_push($previous[$port->containerPort], $portBinding);

                return $previous;
            },
            [],
        )));
    }

    private function setNetworkMode(ContainerParameterDTO $containerParameter, HostConfig $hostConfig, service $service): void
    {
        if ($service->networkMode === null) {
            return;
        }
        $hostConfig->setNetworkMode($this->stringParameterFactory->createFromConfiguration($containerParameter, $service->networkMode));
    }

    private function setEnv(ContainerParameterDTO $containerParameter, ContainersCreatePostBody $container, Service $service): void
    {
        $environments = new ArrayCollection($service->environments);
        if ($environments->isEmpty()) {
            return;
        }
        $container->setEnv($environments->map(fn (Environment $environment) => $this->environmentSpecificationFactory->createFromConfiguration($containerParameter, $environment))->toArray());
    }

    /**
     * @param string[] $labels
     */
    private function setLabel(ContainerParameterDTO $containerParameter, ContainersCreatePostBody $container, Service $service, array $labels): void
    {
        $labels1 = new ArrayCollection($service->labels);
        if ($labels1->isEmpty() && empty($labels)) {
            return;
        }
        $containerLabels = new ArrayObject(array_reduce(
            $labels1->toArray(),
            function (array $labels, Label $label) use ($containerParameter) {
                [$key, $value] = $this->labelSpecificationFactory->createFromConfiguration($containerParameter, $label);
                $labels[$key] = $value;

                return $labels;
            },
            $labels,
        ));
        $container->setLabels($containerLabels);
    }
}
