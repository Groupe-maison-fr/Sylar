<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class FieldVisibility
{
    public function __construct(
        #[Autowire('%security_enabled%')]
        private bool $securityEnabled,
        private Security $security,
    ) {
    }

    /**
     * @param string[] $roles
     */
    public function emptyOnAnyRole(mixed $value, array $roles, mixed $emptyValue = null): mixed
    {
        if (!$this->securityEnabled) {
            return $value;
        }
        foreach ($roles as $role) {
            if ($this->security->isGranted($role, $this->security->getUser())) {
                return $value;
            }
        }

        return $emptyValue;
    }
}
