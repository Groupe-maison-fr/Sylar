<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Models;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Micoli\Elql\Metadata\Table;
use Micoli\Elql\Metadata\Unique;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

#[Table('users')]
#[Unique('[record.getUserIdentifier()]', 'unique user_identifier')]
final class User implements JWTUserInterface, PasswordAuthenticatedUserInterface
{
    private string $username;
    /** @var string[] */
    private array $roles;
    private ?string $password = null;

    /** @param string[] $roles */
    public function __construct(
        string $username,
        array $roles = [],
    ) {
        $this->username = $username;
        $this->roles = $roles;
    }

    /**
     * @param array{roles?: string[]} $payload
     *
     * @return JWTUserInterface|User
     */
    public static function createFromPayload($username, array $payload)
    {
        if (isset($payload['roles'])) {
            return new User($username, (array) $payload['roles']);
        }

        return new User($username);
    }

    #[Ignore]
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    #[Ignore]
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
