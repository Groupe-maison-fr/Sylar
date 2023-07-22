<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\ResolverMap;

use App\UserInterface\GraphQL\Map\AddReservationOutputDTO;
use App\UserInterface\GraphQL\Map\DeleteReservationOutputDTO;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\ForceDestroyContainerOutputDTO;
use App\UserInterface\GraphQL\Map\ForceDestroyFilesystemOutputDTO;
use App\UserInterface\GraphQL\Map\RestartServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\StartServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\StopServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\SuccessOutputDTO;
use Overblog\GraphQLBundle\Resolver\ResolverMap as ResolverMapParent;

final class ResolverMap extends ResolverMapParent
{
    protected function map(): mixed
    {
        return [
            'StartServiceOutput' => $this->resolveAsSuccess(StartServiceSuccessOutputDTO::class),
            'StopServiceOutput' => $this->resolveAsSuccess(StopServiceSuccessOutputDTO::class),
            'RestartServiceOutput' => $this->resolveAsSuccess(RestartServiceSuccessOutputDTO::class),
            'ForceDestroyFilesystemOutput' => $this->resolveAsSuccess(ForceDestroyFilesystemOutputDTO::class),
            'ForceDestroyContainerOutput' => $this->resolveAsSuccess(ForceDestroyContainerOutputDTO::class),
            'AddReservationOutput' => $this->resolveAsSuccess(AddReservationOutputDTO::class),
            'DeleteReservationOutput' => $this->resolveAsSuccess(DeleteReservationOutputDTO::class),
        ];
    }

    /**
     * @return array{'%%resolveType': callable(mixed): ?string}
     */
    public function resolveAsSuccess(string $className): array
    {
        return [
            self::RESOLVE_TYPE => function ($value) use ($className): ?string {
                if ($value instanceof FailedOutputDTO) {
                    return 'FailedOutput';
                }

                if ($value instanceof SuccessOutputDTO) {
                    return 'SuccessOutput';
                }

                if (is_subclass_of($value, $className)) {
                    return 'SuccessOutput';
                }

                return null;
            },
        ];
    }
}
