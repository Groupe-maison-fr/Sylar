<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Listeners;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Overblog\GraphQLBundle\Event\ExecutorResultEvent;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'kernel.event_listener',
    attributes: [
        'event' => 'graphql.post_executor',
        'method' => 'onPostExecutor',
    ],
)]
class GraphqlErrorHandler
{
    public function onPostExecutor(ExecutorResultEvent $event): void
    {
        $myErrorFormatter = fn (Error $error) => FormattedError::createFromException($error);
        $myErrorHandler = fn (array $errors, callable $formatter) => array_map($formatter, $errors);

        $event->getResult()
            ->setErrorFormatter($myErrorFormatter)
            ->setErrorsHandler($myErrorHandler);
    }
}
