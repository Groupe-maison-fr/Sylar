<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Filesystem;

use App\Infrastructure\Filesystem\FilesystemCollection;
use App\Infrastructure\Filesystem\FilesystemDTO;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use App\Infrastructure\Process\Exception\ProcessFailedException;
use App\Infrastructure\Process\Process;
use App\Infrastructure\Process\ProcessInterface;
use Tests\AbstractIntegrationTestCase;

/**
 * @internal
 */
final class ZfsFilesystemServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private FilesystemServiceInterface $zfsFilesystemService;
    private ProcessInterface $process;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zfsFilesystemService = $this->getService(FilesystemServiceInterface::class);
        $this->process = $this->getService(Process::class);
        $this->process->mayRun('zfs', 'destroy', '-R', 'sylar/test');
    }

    protected function tearDown(): void
    {
        $this->process->mayRun('zfs', 'destroy', '-R', 'sylar/test');
    }

    /**
     * @test
     */
    public function it_should_create_a_filesystem(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        self::assertTrue($this->zfsFilesystemService->hasFilesystem('/sylar/test'));
    }

    /**
     * @test
     */
    public function it_should_return_a_filesystem_collection(): void
    {
        self::assertInstanceOf(FilesystemCollection::class, $this->zfsFilesystemService->getFilesystems());
    }

    /**
     * @test
     */
    public function it_should_return_a_single_filesystem_by_name(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $fileSystem = $this->zfsFilesystemService->getFilesystem('sylar/test');
        self::assertInstanceOf(FilesystemDTO::class, $fileSystem);
        self::assertSame('sylar/test', $fileSystem->getName());
    }

    /**
     * @test
     */
    public function it_should_destroy_a_filesystem_by_name(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $this->zfsFilesystemService->destroyFilesystem('sylar/test');
        self::expectException(ProcessFailedException::class);
        $this->zfsFilesystemService->getFilesystem('sylar/test');
    }

    /**
     * @test
     */
    public function it_should_destroy_a_filesystem_and_all_snapshots_by_name(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $this->zfsFilesystemService->createSnapshot('sylar/test', '1');
        $this->zfsFilesystemService->destroyFilesystem('sylar/test', true);
        self::assertFalse($this->zfsFilesystemService->hasSnapshot('sylar/test', '1'));
    }

    /**
     * @test
     */
    public function it_should_snapshot_a_filesystem(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $this->zfsFilesystemService->createSnapshot('sylar/test', '1');
        self::assertTrue($this->zfsFilesystemService->hasSnapshot('sylar/test', '1'));
    }

    /**
     * @test
     */
    public function it_should_get_a_snapshot_collection(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $this->zfsFilesystemService->createSnapshot('sylar/test', 'one');
        $this->zfsFilesystemService->createSnapshot('sylar/test', 'two');
        self::assertInstanceOf(FilesystemCollection::class, $this->zfsFilesystemService->getSnapshots());
    }

    /**
     * @test
     */
    public function it_should_parse_snapshot(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $this->zfsFilesystemService->createSnapshot('sylar/test', 'one');
        $snapshot = $this->zfsFilesystemService->getSnapshot('sylar/test', 'one');
        self::assertSame('sylar/test@one', $snapshot->getName());
        self::assertSame(0, $snapshot->getAvailable());
        self::assertSame(0, $snapshot->getUsed());
        self::assertSame(0, $snapshot->getUsedBySnapshot());
        self::assertSame(0, $snapshot->getUsedByDataset());
        self::assertSame(0, $snapshot->getUsedByRefReservation());
        self::assertSame(0, $snapshot->getUsedByChild());
        self::assertSame(24 * 1024, $snapshot->getRefer());
        self::assertSame('-', $snapshot->getOrigin());
        self::assertSame('-', $snapshot->getMountPoint());
        self::assertSame('snapshot', $snapshot->getType());
    }

    /**
     * @test
     */
    public function it_should_clone_snapshot(): void
    {
        $this->zfsFilesystemService->createFilesystem('sylar/test');
        $this->zfsFilesystemService->createSnapshot('sylar/test', 'one');
        $this->zfsFilesystemService->cloneSnapshot('sylar/test', 'one', 'sylar/testclone_one');
        $this->zfsFilesystemService->getSnapshots('sylar/test')->first();
        self::assertInstanceOf(FilesystemDTO::class, $this->zfsFilesystemService->getClones('sylar/test', 'one')->first());
        self::assertTrue($this->zfsFilesystemService->getClones('sylar/test', 'two')->isEmpty());
    }
}
