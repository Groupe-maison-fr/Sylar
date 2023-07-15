<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloner\Reservation;

use App\Core\ServiceCloner\Reservation\Object\Reservation;
use App\Core\ServiceCloner\Reservation\ReservationRepository;
use Micoli\Elql\Exception\NonUniqueException;
use Micoli\Elql\Metadata\MetadataManager;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
class ServiceReservationRepositoryTest extends AbstractIntegrationTest
{
    private string $databasePath;
    private ReservationRepository $reservationRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databasePath = sprintf('/tmp/test-%s', uniqid());
        $this->cleanupFilesystem();
        $this->reservationRepository = new ReservationRepository($this->databasePath);
        $this->initializeDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanupFilesystem();
    }

    public function testItShouldGetReservedIndexes(): void
    {
        self::assertSame([2, 4], $this->reservationRepository->getReservationIndexesByService('mysql'));
    }

    public function testItShouldDeleteIndex(): void
    {
        $this->reservationRepository->delete('mysql', 'test2', 2);
        self::assertSame([4], $this->reservationRepository->getReservationIndexesByService('mysql'));
    }

    public function testItShouldAddIndex(): void
    {
        $this->reservationRepository->add(new Reservation('mysql', 'test3', 3));
        $this->reservationRepository->add(new Reservation('pgsql', 'test2', 2));
        self::assertSame([2, 3, 4], $this->reservationRepository->getReservationIndexesByService('mysql'));
    }

    public function testItShouldAddIndexIfDuplicated(): void
    {
        try {
            $this->reservationRepository->add(new Reservation('mysql', 'test2', 2));
            self::fail('exception not thrown');
        } catch (NonUniqueException $exception) {
            self::assertSame(
                'Duplicate on [unique service/index:["mysql",2]], [unique service/name:["mysql","test2"]]',
                $exception->getMessage(),
            );
        }
        self::assertSame([2, 4], $this->reservationRepository->getReservationIndexesByService('mysql'));
    }

    private function initializeDatabase(): void
    {
        file_put_contents(
            $this->getReservationYamlPath(),
            <<<BAZ
                -
                    service: mysql
                    name: test2
                    index: 2
                -
                    service: mysql
                    name: test4
                    index: 4
                -
                    service: pgsql
                    name: test1
                    index: 1
                BAZ
        );
    }

    private function getReservationYamlPath(): string
    {
        return sprintf(
            '%s/%s.yaml',
            $this->databasePath,
            (new MetadataManager())->tableNameExtractor(Reservation::class),
        );
    }

    private function cleanupFilesystem(): void
    {
        if (file_exists($this->getReservationYamlPath())) {
            unlink($this->getReservationYamlPath());
        }
        if (file_exists($this->databasePath)) {
            rmdir($this->databasePath);
        }
    }
}
