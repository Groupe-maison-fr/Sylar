<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloner\Reservation;

use App\Core\ServiceCloner\Reservation\Object\Reservation;
use Micoli\Elql\Exception\NonUniqueException;
use Tests\AbstractIntegrationTestCase;
use Tests\ReservationsTestTrait;

/**
 * @internal
 */
class ServiceReservationRepositoryTest extends AbstractIntegrationTestCase
{
    use ReservationsTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reservationsTestSetUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->reservationTearDown();
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
}
