<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages;

use DateTimeInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class FailedMessageDTO
{
    private string $id;
    private string $className;
    private string $message;
    private ?DateTimeInterface $dateTime;
    private ?string $exceptionMessage;
    private ?FlattenException $flattenException;

    public function __construct(
        string $id,
        string $className,
        string $message,
        ?DateTimeInterface $dateTime,
        ?string $exceptionMessage,
        ?FlattenException $flattenException
    ) {
        $this->id = $id;
        $this->className = $className;
        $this->message = $message;
        $this->dateTime = $dateTime;
        $this->exceptionMessage = $exceptionMessage;
        $this->flattenException = $flattenException;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDateTime(): ?DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function getFlattenException(): ?FlattenException
    {
        return $this->flattenException;
    }
}
