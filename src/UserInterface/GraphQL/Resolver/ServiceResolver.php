<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class ServiceResolver implements QueryInterface
{
    private ConfigurationServiceInterface $configurationService;
    private ServiceClonerStateServiceInterface $serviceClonerStateService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ServiceClonerStateServiceInterface $serviceClonerStateService,
    ) {
        $this->configurationService = $configurationService;
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function __invoke(ResolveInfo $info, Service $service, Argument $args)
    {
        switch ($info->fieldName) {
            case 'name':
                return $service->getName();
            case 'image':
                return $service->getImage();
            case 'command':
                return $service->getCommand();
            case 'labels':
                return $service->getLabels();
            case 'ports':
                return $service->getPorts();
            case 'environments':
                return $service->getEnvironments();
            case 'containers':
                return array_filter(
                    $this->serviceClonerStateService->getStates(),
                    fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getMasterName() === $service->getName(),
                );
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    public function resolve(): ArrayCollection
    {
        return $this->configurationService->getConfiguration()->getServices();
    }
}
