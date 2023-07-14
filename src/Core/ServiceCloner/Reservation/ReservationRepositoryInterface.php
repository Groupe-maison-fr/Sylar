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

    public function add(Reservation $reservation): void;

    public function delete(string $service, string $name, int $index): void;
}
