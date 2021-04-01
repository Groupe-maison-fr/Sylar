<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Filesystem;

use App\Core\ServiceCloner\Configuration\ConfigurationService;
use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Filesystem\FilesystemCollection;
use App\Infrastructure\Filesystem\FilesystemDTO;
use App\Infrastructure\Filesystem\FilesystemServiceInterface;
use App\Infrastructure\Process\ProcessInterface;
use App\Infrastructure\Process\SudoProcess;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class ZfsFilesystemServiceIntegrationTest extends AbstractIntegrationTest
{
    private ConfigurationServiceInterface $configurationService;
    private FilesystemServiceInterface $zfsFilesystemService;
    private ProcessInterface $process;
    private string $testRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zfsFilesystemService = $this->getService(FilesystemServiceInterface::class);
        $this->configurationService = $this->getService(ConfigurationService::class);
        $this->process = $this->getService(SudoProcess::class);
        $this->testRoot = '/tmp';

        $this->process->mayRun('zpool', 'destroy', '-f', 'testpool');
        $this->process->mayRun('rm', '-rf', $this->testRoot . '/testdisk');
        $this->process->run('fallocate', '-l', '100M', $this->testRoot . '/testdisk');
        $this->process->run('zpool', 'create', 'testpool', $this->testRoot . '/testdisk');
    }

    protected function tearDown(): void
    {
        $this->process->run('zpool', 'destroy', 'testpool');
        $this->process->run('rm', '-rf', $this->testRoot . '/testdisk');
    }

    /**
     * @test
     */
    public function it_should_create_a_filesystem(): void
    {
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        self::assertTrue($this->zfsFilesystemService->hasFilesystem('/testpool/test'));
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
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $fileSystem = $this->zfsFilesystemService->getFilesystem('testpool/test');
        self::assertInstanceOf(FilesystemDTO::class, $fileSystem);
        self::assertSame('testpool/test', $fileSystem->getName());
    }

    /**
     * @test
     */
    public function it_should_destroy_a_filesystem_by_name(): void
    {
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $this->zfsFilesystemService->destroyFilesystem('testpool/test');
        self::expectException(ProcessFailedException::class);
        $this->zfsFilesystemService->getFilesystem('testpool/test');
    }

    /**
     * @test
     */
    public function it_should_destroy_a_filesystem_and_all_snapshots_by_name(): void
    {
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $this->zfsFilesystemService->createSnapshot('testpool/test', '1');
        $this->zfsFilesystemService->destroyFilesystem('testpool/test', true);
        self::assertFalse($this->zfsFilesystemService->hasSnapshot('testpool/test', '1'));
    }

    /**
     * @test
     */
    public function it_should_snapshot_a_filesystem(): void
    {
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $this->zfsFilesystemService->createSnapshot('testpool/test', '1');
        self::assertTrue($this->zfsFilesystemService->hasSnapshot('testpool/test', '1'));
    }

    /**
     * @test
     */
    public function it_should_get_a_snapshot_collection(): void
    {
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $this->zfsFilesystemService->createSnapshot('testpool/test', 'one');
        $this->zfsFilesystemService->createSnapshot('testpool/test', 'two');
        self::assertInstanceOf(FilesystemCollection::class, $this->zfsFilesystemService->getSnapshots('testpool/test'));
    }

    /**
     * @test
     */
    public function it_should_parse_snapshot(): void
    {
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $this->zfsFilesystemService->createSnapshot('testpool/test', 'one');
        $snapshot = $this->zfsFilesystemService->getSnapshot('testpool/test', 'one');
        self::assertSame('testpool/test@one', $snapshot->getName());
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
        $this->zfsFilesystemService->createFilesystem('testpool/test');
        $this->zfsFilesystemService->createSnapshot('testpool/test', 'one');
        $this->zfsFilesystemService->cloneSnapshot('testpool/test', 'one', 'testpool/testclone_one');
        $snapshot = $this->zfsFilesystemService->getSnapshots('testpool/test')->first();
        self::assertInstanceOf(FilesystemDTO::class, $this->zfsFilesystemService->getClones('testpool/test', 'one')->first());
        self::assertTrue($this->zfsFilesystemService->getClones('testpool/test', 'two')->isEmpty());
    }
}
