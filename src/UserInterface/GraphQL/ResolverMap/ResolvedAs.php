<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\ResolverMap;

use Attribute;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Attribute(Attribute::TARGET_CLASS)]
#[When('never')]
final readonly class ResolvedAs
{
    public function __construct(public string $resolvedType)
    {
    }
}
