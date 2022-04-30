<?php
namespace App\Shared\Domain\Bus\Event;

interface IEvent
{
    public static function from_primitives(
        int $aggregateId,
        array $body,
        string $eventId,
        int $occurredon,
        string $correlationid,
        string $causationid
    ): self;

    public static function event_name(): string;
    public function to_primitives(): array;

    public function aggregate_id(): int;
    public function event_id(): string;
    public function occurred_on(): int;
    public function correlation_id(): string;
    public function causation_id(): string;
}