<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Zfs\ZfsFilesystemCollection;
use App\Infrastructure\Zfs\ZfsFilesystemDTO;
use App\Infrastructure\Zfs\ZfsService;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class StorageResolver implements ResolverInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var ZfsService
     */
    private $zfsService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ZfsService $zfsService
    ) {
        $this->configurationService = $configurationService;
        $this->zfsService = $zfsService;
    }

    public function __invoke(ResolveInfo $info, ZfsFilesystemDTO $zfsFilesystem, Argument $args)
    {
        switch ($info->fieldName) {
            case'name':
                return $zfsFilesystem->getName();
            case'type':
                return $zfsFilesystem->getType();
            case'origin':
                return $zfsFilesystem->getOrigin();
            case'mountPoint':
                return $zfsFilesystem->getMountPoint();
            case'available':
                return $zfsFilesystem->getAvailable();
            case'refer':
                return $zfsFilesystem->getRefer();
            case'used':
                return $zfsFilesystem->getUsed();
            case'usedByChild':
                return $zfsFilesystem->getUsedByChild();
            case'usedByDataset':
                return $zfsFilesystem->getUsedByDataset();
            case'usedByRefreservation':
                return $zfsFilesystem->getUsedByRefreservation();
            case'usedBySnapshot':
                return $zfsFilesystem->getUsedBySnapshot();
        }
    }

    public function resolve(): ZfsFilesystemCollection
    {
        return $this->zfsService->getFilesystems();
    }
}
