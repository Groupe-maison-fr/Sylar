<?php

declare(strict_types=1);

namespace App\Infrastructure\Zfs;

use App\Infrastructure\Process\ProcessInterface;
use Exception;

/**
 * Huge thanks to https://github.com/hashnz/zfs
 */
final class ZfsService implements ZfsServiceInterface
{
    private const headerList = ['name', 'avail', 'used', 'usedsnap', 'usedds', 'usedrefreserv', 'usedchild', 'refer', 'mountpoint', 'origin', 'type'];

    /**
     * @var ProcessInterface
     */
    private $process;

    public function __construct(ProcessInterface $process)
    {
        $this->process = $process;
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

    public function getFilesystem(string $name): ZfsFilesystemDTO
    {
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList), $name)
        )->first();
    }

    public function destroyFilesystem(string $name, bool $force = false): void
    {
        $this->process->run('/sbin/zfs', 'destroy', $name, ($force ? '-R' : null));
    }

    public function createSnapshot(string $name, string $snap): void
    {
        $this->process->run('/sbin/zfs', 'snapshot', sprintf('%s@%s', $name, $snap));
    }

    public function cloneSnapshot(string $name, string $snap, ?string $mountPoint = null): void
    {
        if ($mountPoint === null) {
            $mountPoint = sprintf('%s-%s', $name, $snap);
        }
        $this->process->run('/sbin/zfs', 'clone', sprintf('%s@%s', $name, $snap), $mountPoint);
    }

    public function getClones(string $name, string $snap): ZfsFilesystemCollection
    {
        /* @phpstan-ignore-next-line */
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList))
        )->filter(function (ZfsFilesystemDTO $filesystem) use ($name, $snap) {
            return $filesystem->getOrigin() === sprintf('%s@%s', $name, $snap);
        });
    }

    public function isSnapshoted($name): bool
    {
        try {
            return !$this->getSnapshots($name)->isEmpty();
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getSnapshots(string $name): ZfsFilesystemCollection
    {
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList), '-t', 'snapshot', '-r', $name)
        );
    }

    public function getSnapshot(string $name, string $instance): ?ZfsFilesystemDTO
    {
        $snapshots = $this
            ->getSnapshots($name)
            ->filter(function (ZfsFilesystemDTO $filesystem) use ($name,$instance) {
                return $filesystem->getName() === sprintf('%s@%s', $name, $instance);
            });
        if ($snapshots->isEmpty()) {
            return null;
        }

        return $snapshots->first();
    }

    public function hasSnapshot(string $name, string $instance): bool
    {
        try {
            return $this->getSnapshot($name, $instance) instanceof ZfsFilesystemDTO;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function hasFilesystem(string $name): bool
    {
        return $this
                ->getFilesystems()
                ->filter(function (ZfsFilesystemDTO $filesystem) use ($name) {
                    return $filesystem->getName() === $name;
                })->count() === 1;
    }

    public function getFilesystems(): ZfsFilesystemCollection
    {
        return $this->mapZfsListToZfsCollection(
            $this->process->run('/sbin/zfs', 'list', '-H', '-o', implode(',', self::headerList))
        );
    }

    private function mapZfsListToZfsCollection(string $output): ZfsFilesystemCollection
    {
        return array_reduce(explode(PHP_EOL, trim($output)), function (ZfsFilesystemCollection $collection, string $line) {
            if ($line === '') {
                return $collection;
            }

            $mappedLine = array_combine(self::headerList, preg_split('/\t+/', $line));
            $collection->add(new ZfsFilesystemDTO(
                $mappedLine['name'],
                $mappedLine['avail'],
                $mappedLine['used'],
                $mappedLine['usedsnap'],
                $mappedLine['usedds'],
                $mappedLine['usedrefreserv'],
                $mappedLine['usedchild'],
                $mappedLine['refer'],
                $mappedLine['mountpoint'],
                $mappedLine['origin'],
                $mappedLine['type']
            ));

            return $collection;
        }, new ZfsFilesystemCollection());
    }
}
