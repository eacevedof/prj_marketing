<?php

namespace App\Restrict\Queries\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class QueryWasCreatedEvent extends AbstractEvent
{
    private string $uuid;
    private string $description;
    private string $query;
    private string $total;
    private string $module;

    public function __construct(
        int     $id,
        string  $uuid,
        string  $description,
        string  $query,
        int     $total,
        string  $module,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($id, $eventId, $occurredOn, $correlationId, $causationId);
        $this->uuid = $uuid;
        $this->description = $description;
        $this->query = $query;
        $this->total = $total;
        $this->module = $module;
    }

    public static function eventName(): string
    {
        return "query.created";
    }

    public static function fromPrimitives(
        int     $aggregateId,
        array   $body,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ): AbstractEvent {
        return new self(
            $aggregateId,
            $body["uuid"],
            $body["description"],
            $body["query"],
            $body["total"],
            $body["module"],
            $eventId,
            $occurredOn,
            $correlationId,
            $causationId
        );
    }

    public function toPrimitives(): array
    {
        return [
            "uuid" => $this->uuid,
            "description" => $this->description,
            "query" => $this->query,
            "total" => $this->total,
            "module" => $this->module,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function query(): string
    {
        return $this->query;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function module(): string
    {
        return $this->module;
    }
}
