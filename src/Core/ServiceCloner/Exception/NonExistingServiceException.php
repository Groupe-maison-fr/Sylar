<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Exception;

use DomainException;
use Throwable;

class NonExistingServiceException extends DomainException
{
    public function __construct(string $serviceName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Service %s does not exists', $serviceName), $code, $previous);
    }
}
