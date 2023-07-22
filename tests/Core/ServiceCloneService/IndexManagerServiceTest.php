<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Core\ServiceCloner\IndexManagerService;
use App\Core\ServiceCloner\Reservation\Object\Reservation;
use App\Core\ServiceCloner\Reservation\ReservationRepositoryInterface;
use App\Core\ServiceCloner\ServiceClonerStateService;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use LogicException;
use Mockery;
use Tests\AbstractIntegrationTestCase;

/**
 * @internal
 */
final class IndexManagerServiceTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     *
     * @dataProvider it_should_get_next_available_index_data
     */
    public function it_should_get_next_available_index(array $expectedIndexes, array $reservations, array $usedIndexes): void
    {
        $serviceClonerStateServiceInterface = Mockery::mock(ServiceClonerStateService::class)->makePartial();
        $serviceReservationRepositoryStub = new class($reservations) implements ReservationRepositoryInterface {
            public function __construct(private array $reservations)
            {
            }

            public function getReservationIndexesByService(string $serviceName): array
            {
                return $this->reservations[$serviceName];
            }

            public function findAll(): array
            {
                throw new LogicException('unimplemented');
            }

            public function add(Reservation $reservation): void
            {
                throw new LogicException('unimplemented');
            }

            public function delete(string $service, string $name, int $index): void
            {
                throw new LogicException('unimplemented');
            }
        };
        $indexManagerService = new IndexManagerService(
            $serviceClonerStateServiceInterface,
            $serviceReservationRepositoryStub,
        );
        $serviceClonerStateServiceInterface
            ->shouldReceive('getStates')
            ->andReturn(array_map(
                fn (array $index) => new ServiceClonerStatusDTO(
                    $index[0],
                    '',
                    $index[1],
                    '',
                    '',
                    '',
                    0,
                ),
                $usedIndexes,
            ));
        self::assertSame($expectedIndexes['a'], $indexManagerService->getNextAvailable('a'));
        self::assertSame($expectedIndexes['b'], $indexManagerService->getNextAvailable('b'));
    }

    public static function it_should_get_next_available_index_data(): array
    {
        return [
            [
                ['a' => 1, 'b' => 1],
                ['a' => [], 'b' => []],
                [],
            ],
            [
                ['a' => 4, 'b' => 2],
                ['a' => [1, 2, 3], 'b' => [1]],
                [['a', 1], ['a', 2], ['b', 3]],
            ],
            [
                ['a' => 3, 'b' => 5],
                ['a' => [], 'b' => [2, 4]],
                [['a', 1], ['a', 2], ['b', 1], ['b', 3], ['a', 4], ['a', 5]],
            ],
            [
                ['a' => 7, 'b' => 1],
                ['a' => [], 'b' => []],
                [['a', 1], ['a', 2], ['a', 3], ['b', 3], ['a', 4], ['a', 5], ['a', 6]],
            ],
            [
                ['a' => 4, 'b' => 6],
                ['a' => [], 'b' => [1, 2, 3, 4, 5]],
                [['a', 1], ['a', 2], ['a', 3], ['a', 5], ['a', 6], ['a', 7], ['a', 9]],
            ],
            [
                ['a' => 10, 'b' => 1],
                ['a' => [1, 4, 8], 'b' => []],
                [['a', 9], ['a', 5], ['a', 1], ['a', 3], ['a', 2], ['a', 7], ['a', 6]],
            ],
            [
                ['a' => 1, 'b' => 1],
                ['a' => [], 'b' => []],
                [['a', 9], ['a', 6]],
            ],
            [
                ['a' => 2, 'b' => 2],
                ['a' => [], 'b' => []],
                [['a', 9], ['a', 1], ['b', 1]],
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }
}
