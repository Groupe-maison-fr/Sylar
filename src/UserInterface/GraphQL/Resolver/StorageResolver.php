<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Filesystem\FilesystemCollection;
use App\Infrastructure\Filesystem\FilesystemDTO;
use App\Infrastructure\Filesystem\ZfsFilesystemService;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class StorageResolver implements ResolverInterface
{
    private ConfigurationServiceInterface $configurationService;
    private ZfsFilesystemService $zfsService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ZfsFilesystemService $zfsService
    ) {
        $this->configurationService = $configurationService;
        $this->zfsService = $zfsService;
    }

    public function __invoke(ResolveInfo $info, FilesystemDTO $zfsFilesystem, Argument $args)
    {
        switch ($info->fieldName) {
            case 'name':
                return $zfsFilesystem->getName();
            case 'type':
                return $zfsFilesystem->getType();
            case 'origin':
                return $zfsFilesystem->getOrigin();
            case 'mountPoint':
                return $zfsFilesystem->getMountPoint();
            case 'available':
                return $zfsFilesystem->getAvailable();
            case 'refer':
                return $zfsFilesystem->getRefer();
            case 'used':
                return $zfsFilesystem->getUsed();
            case 'usedByChild':
                return $zfsFilesystem->getUsedByChild();
            case 'usedByDataset':
                return $zfsFilesystem->getUsedByDataset();
            case 'usedByRefreservation':
                return $zfsFilesystem->getUsedByRefreservation();
            case 'usedBySnapshot':
                return $zfsFilesystem->getUsedBySnapshot();
        }
    }

    public function resolve(): FilesystemCollection
    {
        return $this->zfsService->getFilesystems();
    }
}
