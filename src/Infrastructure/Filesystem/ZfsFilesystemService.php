<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

use App\Infrastructure\Process\ProcessInterface;
use DateTimeImmutable;
use Exception;

/**
 * Huge thanks to https://github.com/hashnz/zfs
 */
final class ZfsFilesystemService implements FilesystemServiceInterface
{
    private const headerList = ['name', 'avail', 'used', 'usedsnap', 'usedds', 'usedrefreserv', 'usedchild', 'refer', 'mountpoint', 'origin', 'type', 'creation'];

    public function __construct(
        private ProcessInterface $process,
        private BytesFormatConvertorInterface $sizeFormatConvertor,
    ) {
    }

    public function createFilesystem(string $name): void
    {
        $this->process->run('/sbin/zfs', 'create', $name);
        $this->process->run('/sbin/zfs', 'set', 'snapdir=visible', $name);
    }

    public function createPool(string $pool, string $vdev): void
    {
        $this->process->run('/sbin/zpool', 'create', $pool, $vdev);
    }

    public function getFilesystem(string $name): FilesystemDTO
    {
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList), $name)->getStdOutput(),
        )->first();
    }

    public function destroyFilesystem(string $name, bool $force = false): void
    {
        $this->process->run('/sbin/zfs', 'destroy', $name, $force ? '-Rf' : null);
    }

    public function createSnapshot(string $name, string $snap): void
    {
        $this->process->run('/sbin/zfs', 'snapshot', sprintf('%s@%s', $name, $snap));
    }

    public function destroySnapshot(string $name, string $snap, bool $force = false): void
    {
        $this->process->run('/sbin/zfs', 'destroy', sprintf('%s-%s', $name, $snap), $force ? '-Rf' : null);
        $this->process->run('/sbin/zfs', 'destroy', sprintf('%s@%s', $name, $snap), $force ? '-Rf' : null);
    }

    public function cloneSnapshot(string $name, string $snap, string $mountPoint = null): void
    {
        if ($mountPoint === null) {
            $mountPoint = sprintf('%s-%s', $name, $snap);
        }
        $this->process->run('/sbin/zfs', 'clone', sprintf('%s@%s', $name, $snap), $mountPoint);
    }

    public function getClones(string $name, string $snap): FilesystemCollection
    {
        /** @var FilesystemCollection */
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList))->getStdOutput(),
        )->filter(fn (FilesystemDTO $filesystem) => $filesystem->getOrigin() === sprintf('%s@%s', $name, $snap));
    }

    public function isSnapshoted(string $name): bool
    {
        try {
            return $this->getSnapshot($name) !== null;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getSnapshots(): FilesystemCollection
    {
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList), '-t', 'snapshot')->getStdOutput(),
        );
    }

    public function getSnapshot(string $name, string $instance = null): ?FilesystemDTO
    {
        $snapshotName = $instance === null ? $name : sprintf('%s@%s', $name, $instance);
        $snapshots = $this
            ->getSnapshots()
            ->filter(fn (FilesystemDTO $filesystemDTO) => $filesystemDTO->getName() === $snapshotName);
        if ($snapshots->isEmpty()) {
            return null;
        }

        return $snapshots->first();
    }

    public function hasSnapshot(string $name, string $instance): bool
    {
        try {
            return $this->getSnapshot($name, $instance) instanceof FilesystemDTO;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function hasFilesystem(string $mountPoint): bool
    {
        return $this
                ->getFilesystems()
                ->filter(fn (FilesystemDTO $filesystem) => $filesystem->getMountPoint() === $mountPoint)->count() === 1;
    }

    public function getFilesystems(): FilesystemCollection
    {
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList))->getStdOutput(),
        );
    }

    private function mapZfsListToZfsCollection(string $output): FilesystemCollection
    {
        return array_reduce(explode(PHP_EOL, trim($output)), function (FilesystemCollection $collection, string $line) {
            if ($line === '') {
                return $collection;
            }

            $mappedLine = array_combine(self::headerList, preg_split('/\t+/', $line));

            $strippedCreationString = str_replace('  ', ' ', $mappedLine['creation']);
            $creationTimestamp = DateTimeImmutable::createFromFormat('D F d G:i Y', $strippedCreationString)->getTimestamp();

            $collection->add(new FilesystemDTO(
                $mappedLine['name'],
                $this->sizeFormatConvertor->parse($mappedLine['avail']),
                $this->sizeFormatConvertor->parse($mappedLine['used']),
                $this->sizeFormatConvertor->parse($mappedLine['usedsnap']),
                $this->sizeFormatConvertor->parse($mappedLine['usedds']),
                $this->sizeFormatConvertor->parse($mappedLine['usedrefreserv']),
                $this->sizeFormatConvertor->parse($mappedLine['usedchild']),
                $this->sizeFormatConvertor->parse($mappedLine['refer']),
                $mappedLine['mountpoint'],
                $mappedLine['origin'],
                $mappedLine['type'],
                $creationTimestamp,
            ));

            return $collection;
        }, new FilesystemCollection());
    }
}
