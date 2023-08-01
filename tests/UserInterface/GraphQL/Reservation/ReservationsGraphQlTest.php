<?php

declare(strict_types=1);

namespace Tests\UserInterface\GraphQL\Reservation;

use App\Core\ServiceCloner\Reservation\ReservationRepositoryInterface;
use Micoli\Trail\Trail;
use Tests\GraphQlTestCase;
use Tests\ReservationsTestTrait;

/**
 * @internal
 */
class ReservationsGraphQlTest extends GraphQlTestCase
{
    use ReservationsTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reservationsTestSetUp();
        $this->graphQlSetService(ReservationRepositoryInterface::class, $this->reservationRepository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->reservationTearDown();
    }

    public function testItShouldGetReservation(): void
    {
        $result = $this->getGraphQlReservationsQuery();
        $this->assertMatchesPattern(
            [
                [
                    'service' => 'mysql',
                    'name' => 'test2',
                    'index' => 2,
                ],
                [
                    'service' => 'mysql',
                    'name' => 'test4',
                    'index' => 4,
                ],
                [
                    'service' => 'pgsql',
                    'name' => 'test1',
                    'index' => 1,
                ],
            ],
            $result,
        );
    }

    public function testItShouldAddAReservation(): void
    {
        $result = $this->graphQlMutation(
            <<<'GRAPHQL'
                mutation MutationAddReservation(
                    $service: String!
                    $index: Int!
                    $name: String!
                  ) {
                    addReservation(
                      input: { service: $service, index: $index, name: $name }
                    ) {
                      ... on SuccessOutput {
                        success
                      }
                    }
                }
                GRAPHQL,
            [
                'service' => 'mysql',
                'index' => 11,
                'name' => 'foobar',
            ],
        );
        self::assertTrue(Trail::eval($result, '[data][addReservation][success]'));

        self::assertSame([
            'service' => 'mysql',
            'name' => 'foobar',
            'index' => 11,
        ], Trail::eval($this->getGraphQlReservationsQuery(), '@last'));
    }

    public function testItShouldDeleteAReservation(): void
    {
        $result = $this->graphQlMutation(
            <<<'GRAPHQL'
                mutation MutationDeleteReservation(
                    $service: String!
                    $index: Int!
                    $name: String!
                  ) {
                    deleteReservation(
                      input: { service: $service, index: $index, name: $name }
                    ) {
                      ... on SuccessOutput {
                        success
                      }
                    }
                }
                GRAPHQL,
            [
                'service' => 'mysql',
                'index' => 2,
                'name' => 'test2',
            ],
        );
        self::assertSame(true, $result['data']['deleteReservation']['success']);
        self::assertSame(2, Trail::eval($this->getGraphQlReservationsQuery(), '@count'));
    }

    private function getGraphQlReservationsQuery(): array
    {
        return $this->graphQlQuery(
            <<<GRAPHQL
                    query reservations {
                        reservations {
                            service
                            name
                            index
                        }
                    }
                GRAPHQL
        )['data']['reservations'];
    }
}
