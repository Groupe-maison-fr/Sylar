<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Infrastructure\Security\Models\User;
use Micoli\Elql\Elql;
use Micoli\Elql\Encoder\YamlEncoder;
use Micoli\Elql\Metadata\MetadataManager;
use Micoli\Elql\Persister\FilePersister;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class UserRepository implements UserRepositoryInterface
{
    private Elql $database;

    public function __construct(
        string $containerDatabasePath,
    ) {
        if (!file_exists($containerDatabasePath)) {
            mkdir($containerDatabasePath);
        }
        $this->database = new Elql(
            new FilePersister(
                $containerDatabasePath,
                new MetadataManager(),
                YamlEncoder::FORMAT,
            ),
        );
    }

    public function getByUsername(string $username): ?User
    {
        $values = $this->database->find(User::class, sprintf('record.getUserIdentifier() == "%s"', $username));
        if (count($values) === 0) {
            return null;
        }
        if (count($values) !== 1) {
            throw new TooManyRequestsHttpException();
        }

        return $values[0];
    }

    public function add(User $user): void
    {
        $this->database->add($user);
        $this->database->persister->flush();
    }

    public function update(User $user): void
    {
        $this->database->update(User::class, function (User $record) use ($user): User {
            $record->setPassword($user->getPassword());
            $record->setRoles($user->getRoles());

            return $record;
        }, sprintf('record.getUserIdentifier() == "%s"', $user->getUserIdentifier()));
        $this->database->persister->flush();
    }

    public function delete(string $username): void
    {
        $this->database->delete(User::class, sprintf('record.getUserIdentifier() == "%s"', $username));
        $this->database->persister->flush();
    }
}
