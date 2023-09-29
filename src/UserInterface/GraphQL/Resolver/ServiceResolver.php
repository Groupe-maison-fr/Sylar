<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use App\UserInterface\GraphQL\Security\FieldVisibility;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final readonly class ServiceResolver implements QueryInterface
{
    public function __construct(
        private ConfigurationServiceInterface $configurationService,
        private ServiceClonerStateServiceInterface $serviceClonerStateService,
        private FieldVisibility $fieldVisibility,
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
                return $this->fieldVisibility->emptyOnAnyRole($service->labels, ['ROLE_ADMIN', 'ROLE_USER'], []);
            case 'ports':
                return $service->ports;
            case 'environments':
                return $this->fieldVisibility->emptyOnAnyRole($service->environments, ['ROLE_ADMIN', 'ROLE_USER'], []);
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
