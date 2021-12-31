<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\FailedMessages;

use DateTimeImmutable;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Serialization\Normalizer\FlattenExceptionNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeZoneNormalizer;
use Symfony\Component\Serializer\Normalizer\FormErrorNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class FailedMessageDTOFactory implements FailedMessageDTOFactoryInterface
{
    private SerializerInterface $serializer;

    public function __construct(
    ) {
        $this->serializer = new Serializer([
            new UnwrappingDenormalizer(),
            new FlattenExceptionNormalizer(),
            new ProblemNormalizer(),
            new JsonSerializableNormalizer(),
            new DateTimeNormalizer(),
            new ConstraintViolationListNormalizer(),
            new DateTimeZoneNormalizer(),
            new DateIntervalNormalizer(),
            new FormErrorNormalizer(),
            new DataUriNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(),
        ], [
            new JsonEncoder(),
        ]);
    }

    public function create(Envelope $envelope): FailedMessageDTO
    {
        $lastRedeliveryStampWithException = $this->getLastRedeliveryStampWithException($envelope);

        return new FailedMessageDTO(
            $this->getMessageId($envelope),
            get_class($envelope->getMessage()),
            $this->serializer->serialize($envelope->getMessage(), 'json', ['json_encode_options' => JSON_PRETTY_PRINT]),
            new DateTimeImmutable(),
            $lastRedeliveryStampWithException ? $lastRedeliveryStampWithException->getExceptionMessage() : null,
            $lastRedeliveryStampWithException ? $lastRedeliveryStampWithException->getFlattenException() : null,
        );
    }

    private function getLastRedeliveryStampWithException(Envelope $envelope): ?ErrorDetailsStamp
    {
        /** @var ErrorDetailsStamp $stamp */
        foreach (array_reverse($envelope->all(ErrorDetailsStamp::class)) as $stamp) {
            return $stamp;
        }

        return null;
    }

    private function getMessageId(Envelope $envelope): string
    {
        /** @var TransportMessageIdStamp|null $stamp */
        $stamp = $envelope->last(TransportMessageIdStamp::class);

        return $stamp ? $stamp->getId() : 'null';
    }
}
