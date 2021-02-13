<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStateService;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class ContainerResolver implements ResolverInterface
{
    private ConfigurationServiceInterface $configurationService;
    private ServiceClonerStateService $serviceClonerStateService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ServiceClonerStateService $serviceClonerStateService
    ) {
        $this->configurationService = $configurationService;
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function __invoke(ResolveInfo $info, ServiceClonerStatusDTO $state, Argument $args)
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
            case 'zfsFilesystemName':
                return $state->getZfsFilesystemName();
            case 'zfsFilesystem':
                return $state->getZfsFilesystem();
            case 'time':
                return $state->getCreatedAt();
        }
    }

    public function resolve(): array
    {
        return $this->serviceClonerStateService->getStates();
    }
}
