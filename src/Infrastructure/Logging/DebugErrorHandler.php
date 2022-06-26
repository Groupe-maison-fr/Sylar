<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Symfony\Component\ErrorHandler\BufferingLogger;
use Symfony\Component\ErrorHandler\ErrorHandler;

/**
 * This class is a copy of the original Symfony\Component\ErrorHandler\Debug
 * class with the difference that it sets a null logger for deprecations.
 * At the time of writing it's still impossible to silence deprecations in the
 * symfony console which leads to noisy output when running console commands
 * with debug enabled.
 *
 * For more information see https://github.com/symfony/symfony/issues/35575.
 */
class DebugErrorHandler
{
    public static function enable(bool $showDeprecations = true): ErrorHandler
    {
        $errorHandler = new ErrorHandler(new BufferingLogger(), true);
        if (!$showDeprecations) {
            $errorHandler->setLoggers([
                \E_DEPRECATED => null,
                \E_USER_DEPRECATED => null,
            ]);
        }

        return ErrorHandler::register($errorHandler);
    }
}
