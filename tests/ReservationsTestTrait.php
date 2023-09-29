<?php

declare(strict_types=1);

namespace Tests;

use App\Core\ServiceCloner\Reservation\Object\Reservation;
use App\Core\ServiceCloner\Reservation\ReservationRepository;
use Micoli\Elql\Metadata\MetadataManager;

/**
 * @internal
 */
trait ReservationsTestTrait
{
    private string $databasePath;
    private ReservationRepository $reservationRepository;

    protected function reservationsTestSetUp(): void
    {
        $this->databasePath = sprintf('/tmp/test-%s', uniqid());
        $this->cleanupReservationFilesystem();
        $this->reservationRepository = new ReservationRepository($this->databasePath);
        $this->initializeReservationDatabase();
    }

    protected function reservationTearDown(): void
    {
        $this->cleanupReservationFilesystem();
    }

    protected function initializeReservationDatabase(): void
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

    protected function getReservationYamlPath(): string
    {
        return sprintf(
            '%s/%s.yaml',
            $this->databasePath,
            (new MetadataManager())->tableNameExtractor(Reservation::class),
        );
    }

    protected function cleanupReservationFilesystem(): void
    {
        if (file_exists($this->getReservationYamlPath())) {
            unlink($this->getReservationYamlPath());
        }
        if (file_exists($this->databasePath)) {
            rmdir($this->databasePath);
        }
    }
}
