<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Resolver;

use Docker\API\Endpoint\ContainerStats;
use Docker\API\Model\ContainerSummaryItem;
use Docker\Docker;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

final class ContainerStatisticResolver implements ResolverInterface
{
    private Docker $docker;

    public function __construct(
        Docker $docker
    ) {
        $this->docker = $docker;
    }

    public function __invoke(ResolveInfo $info, ContainerSummaryItem $containerSummaryItem, Argument $args)
    {
        /** @var ContainerStats $stat */
        $stat = $this->docker->containerStats($containerSummaryItem->getId(), ['stream' => false]);
        dd($stat);
        switch ($info->fieldName) {
            case 'containerName':
                return implode(',', $containerSummaryItem->getNames());
            case 'stat':
                return $this->docker->containerStats($containerSummaryItem->getId(), ['stream' => false]);
        }
    }

    public function resolve(): array
    {
        return $this->docker->containerList();
    }
}
