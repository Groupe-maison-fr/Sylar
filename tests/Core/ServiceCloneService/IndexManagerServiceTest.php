<?php

declare(strict_types=1);

namespace Tests\Core\ServiceCloneService;

use App\Core\ServiceCloner\IndexManagerService;
use App\Core\ServiceCloner\ServiceClonerStateServiceInterface;
use App\Core\ServiceCloner\ServiceClonerStatusDTO;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class IndexManagerServiceTest extends AbstractIntegrationTest
{
    /**
     * @test
     * @dataProvider it_should_get_next_available_index_data
     */
    public function it_should_get_next_available_index(int $expectedIndex, array $usedIndexes): void
    {
        $serviceClonerStateServiceInterface = \Mockery::mock(ServiceClonerStateServiceInterface::class);
        $indexManagerService = new IndexManagerService($serviceClonerStateServiceInterface);
        $serviceClonerStateServiceInterface
            ->shouldReceive('getStates')
            ->andReturn(array_map(fn (int $index) => new ServiceClonerStatusDTO(
                '',
                '',
                $index,
                '',
                '',
                '',
                0
            ), $usedIndexes));
        self::assertSame($expectedIndex, $indexManagerService->getNextAvailable());
    }

    public function it_should_get_next_available_index_data(): array
    {
        return [
            [1, []],
            [3, [1, 2]],
            [3, [1, 2, 4, 5]],
            [7, [1, 2, 3, 4, 5, 6]],
            [4, [1, 2, 3, 5, 6, 7, 9]],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }
}
