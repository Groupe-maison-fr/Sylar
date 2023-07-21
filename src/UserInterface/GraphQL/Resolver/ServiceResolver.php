<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class ServiceResolver implements QueryInterface
{
    public function __construct(
        private ConfigurationServiceInterface $configurationService,
        private ServiceClonerStateServiceInterface $serviceClonerStateService,
    ) {
    }

    public function __invoke(ResolveInfo $info, Service $service, Argument $args): mixed
    {
        switch ($info->fieldName) {
            case 'name':
                return $service->name;
            case 'image':
                return $service->image;
            case 'command':
                return $service->command;
            case 'labels':
                return $service->labels;
            case 'ports':
                return $service->ports;
            case 'environments':
                return $service->environments;
            case 'containers':
                return array_filter(
                    $this->serviceClonerStateService->getStates(),
                    fn (ServiceClonerStatusDTO $serviceClonerStatusDTO) => $serviceClonerStatusDTO->getMasterName() === $service->name,
                );
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    /**
     * @return Service[]
     */
    public function resolve(): array
    {
        return $this->configurationService->getConfiguration()->services;
    }
}
