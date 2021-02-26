<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\ResolverMap;

use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use App\UserInterface\GraphQL\Map\RestartServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\StartServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\StopServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\SuccessOutputDTO;
use Overblog\GraphQLBundle\Resolver\ResolverMap as ResolverMapParent;

final class ResolverMap extends ResolverMapParent
{
    private function isGenericMap($value): ?string
    {
        if ($value instanceof FailedOutputDTO) {
            return 'FailedOutput';
        }

        if ($value instanceof SuccessOutputDTO) {
            return 'SuccessOutput';
        }

        return null;
    }

    protected function map()
    {
        return [
            'StartServiceOutput' => [
                self::RESOLVE_TYPE => function ($value): ?string {
                    $genericType = $this->isGenericMap($value);
                    if ($genericType !== null) {
                        return $genericType;
                    }
                    if ($value instanceof StartServiceSuccessOutputDTO) {
                        return 'SuccessOutput';
                    }

                    return null;
                },
            ],
            'StopServiceOutput' => [
                self::RESOLVE_TYPE => function ($value): ?string {
                    $genericType = $this->isGenericMap($value);
                    if ($genericType !== null) {
                        return $genericType;
                    }
                    if ($value instanceof StopServiceSuccessOutputDTO) {
                        return 'SuccessOutput';
                    }

                    return null;
                },
            ],
            'RestartServiceOutput' => [
                self::RESOLVE_TYPE => function ($value): ?string {
                    $genericType = $this->isGenericMap($value);
                    if ($genericType !== null) {
                        return $genericType;
                    }
                    if ($value instanceof RestartServiceSuccessOutputDTO) {
                        return 'SuccessOutput';
                    }

                    return null;
                },
            ],
        ];
    }
}
