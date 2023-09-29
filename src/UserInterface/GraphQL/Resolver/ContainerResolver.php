<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\ServiceClonerStateService;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final readonly class ContainerResolver implements QueryInterface
{
    public function __construct(
        private ServiceClonerStateService $serviceClonerStateService,
    ) {
    }

    public function __invoke(ResolveInfo $info, ServiceClonerStatusDTO $state, Argument $args): mixed
    {
        switch ($info->fieldName) {
            case 'containerName':
                return $state->getContainerName();
            case 'masterName':
                return $state->getMasterName();
            case 'instanceName':
                return $state->getInstanceName();
            case 'instanceIndex':
                return $state->getIndex();
            case 'isMaster':
                return $state->isMaster();
            case 'dockerState':
                return $state->getDockerState();
            case 'exposedPorts':
                return $state->getExposedPorts();
            case 'zfsFilesystemName':
                return $state->getZfsFilesystemName();
            case 'zfsFilesystem':
                return $state->getZfsFilesystem();
            case 'time':
                return $state->getCreatedAt();
            case 'uptime':
                return time() - $state->getCreatedAt();
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    /**
     * @return ServiceClonerStatusDTO[]
     */
    public function resolve(): array
    {
        return $this->serviceClonerStateService->getStates();
    }
}
