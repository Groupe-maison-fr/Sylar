<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use App\Infrastructure\Filesystem\FilesystemCollection;
use App\Infrastructure\Filesystem\FilesystemDTO;
use App\Infrastructure\Filesystem\ZfsFilesystemService;
use DomainException;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class StorageResolver implements QueryInterface
{
    public function __construct(
        private ZfsFilesystemService $zfsService,
    ) {
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
            case 'creationTimestamp':
                return $zfsFilesystem->getCreationTimestamp();
        }
        throw new DomainException(sprintf('No field %s found', $info->fieldName));
    }

    public function resolve(): FilesystemCollection
    {
        return $this->zfsService->getFilesystems();
    }
}
