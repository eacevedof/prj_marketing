<?php

namespace App\Shared\Domain\Bus\Event;

interface IEvent
{
    public static function fromPrimitives(
        int    $aggregateId,
        array  $body,
        string $eventId,
        int    $occurredOn,
        string $correlationId,
        string $causationId
    ): self;

    public static function eventName(): string;
    public function toPrimitives(): array;

    public function aggregateId(): int;
    public function eventId(): string;
    public function occurredOn(): int;
    public function correlationId(): string;
    public function causationId(): string;
}
