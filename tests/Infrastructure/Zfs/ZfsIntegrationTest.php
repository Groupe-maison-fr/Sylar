<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Zfs;

use App\Core\ServiceCloner\Configuration\ConfigurationService;
use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use App\Infrastructure\Process\ProcessInterface;
use App\Infrastructure\Process\SudoProcess;
use App\Infrastructure\Zfs\ZfsFilesystemCollection;
use App\Infrastructure\Zfs\ZfsFilesystemDTO;
use App\Infrastructure\Zfs\ZfsServiceInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class ZfsIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var ZfsServiceInterface
     */
    private $zfsService;

    /**
     * @var ProcessInterface
     */
    private $process;

    /**
     * @var string
     */
    private $testRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->zfsService = $this->getService(ZfsServiceInterface::class);
        $this->configurationService = $this->getService(ConfigurationService::class);
        $this->process = $this->getService(SudoProcess::class);
        $this->testRoot = '/tmp';

        $this->process->mayRun('zpool', 'destroy', '-Rf', 'testpool');
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
        $this->zfsService->createFilesystem('testpool/test');
        self::assertTrue($this->zfsService->hasFilesystem('testpool/test'));
    }

    /**
     * @test
     */
    public function it_should_return_a_filesystem_collection(): void
    {
        self::assertInstanceOf(ZfsFilesystemCollection::class, $this->zfsService->getFilesystems());
    }

    /**
     * @test
     */
    public function it_should_return_a_single_filesystem_by_name(): void
    {
        $this->zfsService->createFilesystem('testpool/test');
        $fileSystem = $this->zfsService->getFilesystem('testpool/test');
        self::assertInstanceOf(ZfsFilesystemDTO::class, $fileSystem);
        self::assertSame('testpool/test', $fileSystem->getName());
    }

    /**
     * @test
     */
    public function it_should_destroy_a_filesystem_by_name(): void
    {
        $this->zfsService->createFilesystem('testpool/test');
        $this->zfsService->destroyFilesystem('testpool/test');
        self::expectException(ProcessFailedException::class);
        $this->zfsService->getFilesystem('testpool/test');
    }

    /**
     * @test
     */
    public function it_should_destroy_a_filesystem_and_all_snapshots_by_name(): void
    {
        $this->zfsService->createFilesystem('testpool/test');
        $this->zfsService->createSnapshot('testpool/test', '1');
        $this->zfsService->destroyFilesystem('testpool/test', true);
        self::expectException(ProcessFailedException::class);
        $this->zfsService->getFilesystem('testpool/test');
    }

    /**
     * @test
     */
    public function it_should_snapshot_a_filesystem(): void
    {
        $this->zfsService->createFilesystem('testpool/test');
        $this->zfsService->createSnapshot('testpool/test', '1');
        self::assertTrue($this->zfsService->hasSnapshot('testpool/test', '1'));
    }

    /**
     * @test
     */
    public function it_should_get_a_snapshot_collection(): void
    {
        $this->zfsService->createFilesystem('testpool/test');
        $this->zfsService->createSnapshot('testpool/test', 'one');
        $this->zfsService->createSnapshot('testpool/test', 'two');
        self::assertInstanceOf(ZfsFilesystemCollection::class, $this->zfsService->getSnapshots('testpool/test'));
    }

    /**
     * @test
     */
    public function it_should_clone_snapshot(): void
    {
        $this->zfsService->createFilesystem('testpool/test');
        $this->zfsService->createSnapshot('testpool/test', 'one');
        $this->zfsService->cloneSnapshot('testpool/test', 'one', 'testpool/testclone_one');
        $snapshot = $this->zfsService->getSnapshots('testpool/test')->first();
        self::assertInstanceOf(ZfsFilesystemDTO::class, $this->zfsService->getClones('testpool/test', 'one')->first());
        self::assertTrue($this->zfsService->getClones('testpool/test', 'two')->isEmpty());
    }
}
