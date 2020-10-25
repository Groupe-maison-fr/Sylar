<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStateService;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class ContainerResolver implements ResolverInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var ServiceClonerStateService
     */
    private $serviceClonerStateService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ServiceClonerStateService $serviceClonerStateService
    ) {
        $this->configurationService = $configurationService;
        $this->serviceClonerStateService = $serviceClonerStateService;
    }

    public function __invoke(ResolveInfo $info, $state, Argument $args)
    {
        switch ($info->fieldName) {
            case 'containerName':
                return $state['containerName'];
            case 'masterName':
                return $state['masterName'];
            case 'instanceName':
                return $state['instanceName'];
            case 'instanceIndex':
                return $state['instanceIndex'];
            case 'zfsFilesystemName':
                return $state['zfsFilesystemName'];
            case 'zfsFilesystem':
                return $state['zfsFilesystem'];
            case 'time':
                return $state['time'];
        }
    }

    public function resolve(): array
    {
        return $this->serviceClonerStateService->getStates();
    }
}
