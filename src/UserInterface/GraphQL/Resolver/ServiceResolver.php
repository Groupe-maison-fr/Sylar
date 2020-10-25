<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Core\ServiceCloner\Configuration\Object\Service;
use Doctrine\Common\Collections\ArrayCollection;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class ServiceResolver implements ResolverInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    public function __construct(
        ConfigurationServiceInterface $configurationService
    ) {
        $this->configurationService = $configurationService;
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
        }
    }

    public function resolve(): ArrayCollection
    {
        return $this->configurationService->getConfiguration()->getServices();
    }
}
