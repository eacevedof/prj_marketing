<?php

namespace App\Restrict\Queries\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class QueryActionWasCreatedEvent extends AbstractEvent
{
    private int $idQuery;
    private string $description;
    private string $params;

    public function __construct(
        int     $id,
        int     $idQuery,
        string  $description,
        string  $params,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($id, $eventId, $occurredOn, $correlationId, $causationId);
        $this->idQuery = $idQuery;
        $this->description = $description;
        $this->params = $params;
    }

    public static function eventName(): string
    {
        return "queryaction.created";
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
            $body["id_query"],
            $body["description"],
            $body["params"],
            $eventId,
            $occurredOn,
            $correlationId,
            $causationId
        );
    }

    public function toPrimitives(): array
    {
        return [
            "id_query" => $this->idQuery,
            "description" => $this->description,
            "params" => $this->params,
        ];
    }

    public function idQuery(): int
    {
        return $this->idQuery;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function params(): string
    {
        return $this->params;
    }
}
