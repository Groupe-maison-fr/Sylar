<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Reservation;

use App\Core\ServiceCloner\Reservation\Object\Reservation;

interface ReservationRepositoryInterface
{
    /**
     * @return int[]
     */
    public function getReservationIndexesByService(string $serviceName): array;

    /**
     * @return Reservation[]
     */
    public function findAll(): array;
}
