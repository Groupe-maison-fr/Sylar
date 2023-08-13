<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Security;

use Symfony\Bundle\SecurityBundle\Security;

final readonly class FieldVisibility
{
    public function __construct(
        private Security $security,
    ) {
    }

    /**
     * @param string[] $roles
     */
    public function emptyOnAnyRole(mixed $value, array $roles, mixed $emptyValue = null): mixed
    {
        foreach ($roles as $role) {
            if ($this->security->isGranted($role, $this->security->getUser())) {
                return $value;
            }
        }

        return $emptyValue;
    }
}
