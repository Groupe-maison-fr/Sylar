<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages\Graphql\Resolver;

use App\Infrastructure\Messenger\FailedMessages\FailedMessageDTO;
use App\Infrastructure\Messenger\FailedMessages\Repository\FailedMessagesRepositoryInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

final class MessagesResolver implements QueryInterface
{
    public function __construct(
        private FailedMessagesRepositoryInterface $failedMessagesRepository,
    ) {
    }

    public function __invoke(ResolveInfo $info, FailedMessageDTO $failedMessage, Argument $args)
    {
        switch ($info->fieldName) {
            case 'id':
                return $failedMessage->getId();
            case 'className':
                return $failedMessage->getClassName();
            case 'message':
                return $failedMessage->getMessage();
            case 'exceptionMessage':
                return $failedMessage->getExceptionMessage();
            case 'flattenException':
                return $failedMessage->getFlattenException();
            case 'backtrace':
                return $failedMessage->getFlattenException()->getTrace();
            case 'date':
                return $failedMessage->getDateTime();
        }
    }

    public function resolve(int $id): FailedMessageDTO
    {
        return $this->failedMessagesRepository->getById($id);
    }

    public function findAll(int $max): array
    {
        return $this->failedMessagesRepository->findAll($max);
    }
}
