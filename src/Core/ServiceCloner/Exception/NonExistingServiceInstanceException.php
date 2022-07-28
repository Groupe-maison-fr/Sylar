<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Exception;

use DomainException;
use Throwable;

final class NonExistingServiceInstanceException extends DomainException
{
    public function __construct(string $serviceName, string $instanceName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Service instance %s@%s does not exists', $serviceName, $instanceName), $code, $previous);
    }
}
