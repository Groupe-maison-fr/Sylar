<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Infrastructure\Security\Models\User;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Webmozart\Assert\Assert;

final class UserService
{
    /**
     * @param array<string, string[]> $hierarchyRoles
     */
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire('%security.role_hierarchy.roles%')]
        private readonly array $hierarchyRoles,
    ) {
    }

    /**
     * @return string[]
     */
    private function getRoles(): array
    {
        $roles = [];
        foreach ($this->hierarchyRoles as $role => $subRoles) {
            $roles = [...$roles, $role, ...$subRoles];
        }

        return array_unique($roles);
    }

    public function addUser(User $user): void
    {
        $this->assertUserIsValid($user);
        $this->assertUserDoesNotExists($user);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $this->repository->add($user);
    }

    public function updateUser(User $user): void
    {
        $this->assertUserIsValid($user);
        $this->assertUserExists($user);
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        $this->repository->update($user);
    }

    public function deleteUser(User $user): void
    {
        $this->assertUserExists($user);
        $this->repository->delete($user->getUsername());
    }

    private function assertUserDoesNotExists(User $user): void
    {
        $existingUser = $this->repository->getByUsername($user->getUserIdentifier());
        if ($existingUser !== null) {
            throw new LogicException(sprintf('<error>User "%s" already exists</error>', $user->getUserIdentifier()));
        }
    }

    private function assertUserExists(User $user): void
    {
        $existingUser = $this->repository->getByUsername($user->getUserIdentifier());
        if ($existingUser === null) {
            throw new LogicException(sprintf('<error>User "%s" does not exists</error>', $user->getUserIdentifier()));
        }
    }

    private function assertUserIsValid(User $user): void
    {
        Assert::minLength($user->getUserIdentifier(), 2);
        Assert::minLength($user->getPassword(), 6);
        Assert::allInArray($user->getRoles(), $this->getRoles());
    }
}
