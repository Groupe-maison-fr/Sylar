<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Infrastructure\Security\Models\User;

interface UserRepositoryInterface
{
    public function getByUsername(string $username): ?User;

    public function add(User $user): void;

    public function update(User $user): void;

    public function delete(string $username): void;
}
