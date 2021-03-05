<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

interface FilesystemServiceInterface
{
    public function createFilesystem(string $name): void;

    public function createPool(string $pool, string $vdev): void;

    public function getFilesystem(string $name): FilesystemDTO;

    public function destroyFilesystem(string $name, bool $force = false): void;

    public function createSnapshot(string $name, string $snap): void;

    public function destroySnapshot(string $name, string $snap, bool $force = false): void;

    public function isSnapshoted($name): bool;

    public function getSnapshots(): FilesystemCollection;

    public function getSnapshot(string $name, ?string $instance = null): ?FilesystemDTO;

    public function hasSnapshot(string $name, string $instance): bool;

    public function cloneSnapshot(string $name, string $snap, ?string $mountPoint = null): void;

    public function getClones(string $name, string $snap): FilesystemCollection;

    public function hasFilesystem(string $mountPoint): bool;

    public function getFilesystems(): FilesystemCollection;
}
