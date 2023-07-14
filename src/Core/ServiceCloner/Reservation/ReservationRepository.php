<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Reservation;

use App\Core\ServiceCloner\Reservation\Object\Reservation;
use Micoli\Elql\Elql;
use Micoli\Elql\Encoder\YamlEncoder;
use Micoli\Elql\Metadata\MetadataManager;
use Micoli\Elql\Persister\FilePersister;

final class ReservationRepository implements ReservationRepositoryInterface
{
    private Elql $database;

    public function __construct(
        string $containerConfigurationPath,
    ) {
        $path = $containerConfigurationPath . '/database';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $this->database = new Elql(
            new FilePersister(
                $path,
                new MetadataManager(),
                YamlEncoder::FORMAT,
            ),
        );
    }

    /**
     * @return int[]
     */
    public function getReservationIndexesByService(string $serviceName): array
    {
        return array_values(array_map(
            fn (Reservation $reservation) => $reservation->getIndex(),
            $this->database->find(Reservation::class, sprintf('record.getService() == "%s"', $serviceName)),
        ));
    }

    public function findAll(): array
    {
        return $this->database->find(Reservation::class);
    }

    public function add(Reservation $reservation): void
    {
        $this->database->add($reservation);
        $this->database->persister->flush();
    }

    public function delete(string $service, string $name, int $index): void
    {

    }
}
