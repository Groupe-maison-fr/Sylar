<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

use DomainException;

final class UnitFormatException extends DomainException
{
    public function __construct(
        string $formattedSize,
    ) {
        parent::__construct(sprintf('Size "%s" could not be parsed', $formattedSize));
    }
}
