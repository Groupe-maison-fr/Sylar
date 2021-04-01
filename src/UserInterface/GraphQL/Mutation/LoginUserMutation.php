<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

final class LoginUserMutation implements MutationInterface
{
    public function __construct(
    ) {
    }

    public function __invoke(string $username, string $password): void
    {
        try {
            //return new AuthenticationMutationSuccessOutput($tokenDTO);
        } catch (\Exception $exception) {
            //return new AuthenticationMutationFailedOutput();
        }
    }
}
