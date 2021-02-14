<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Mutation;

use App\Core\ServiceCloner\ServiceClonerServiceInterface;
use App\UserInterface\GraphQL\Map\StartServiceSuccessOutputDTO;
use App\UserInterface\GraphQL\Map\FailedOutputDTO;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

final class StartServiceMutation implements MutationInterface
{
    private ServiceClonerServiceInterface $serviceClonerService;

    public function __construct(
        ServiceClonerServiceInterface $serviceClonerService
    ) {
        $this->serviceClonerService = $serviceClonerService;
    }

    public function __invoke(string $masterName, string $instanceName, ?int $index)
    {
        try {
            $this->serviceClonerService->startService(
                $masterName,
                $instanceName,
                $index === null ? null : (int) $index
            );

            return new StartServiceSuccessOutputDTO(true);
        } catch (\Exception $exception) {
            return new FailedOutputDTO(1, $exception->getMessage());
        }
    }
}
