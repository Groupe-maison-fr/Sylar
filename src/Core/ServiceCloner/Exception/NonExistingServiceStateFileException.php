<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Exception;

use DomainException;
use Throwable;

final class NonExistingServiceStateFileException extends DomainException
{
    public function __construct(string $serviceName, string $instanceName, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Service state instance %s@%s does not exists', $serviceName, $instanceName), $code, $previous);
    }
}
