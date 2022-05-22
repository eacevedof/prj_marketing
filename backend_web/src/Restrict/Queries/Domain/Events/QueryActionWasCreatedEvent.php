<?php
namespace App\Restrict\Queries\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class QueryActionWasCreatedEvent extends AbsEvent
{
    private int $idquery;
    private string $description;
    private string $params;

    public function __construct(
        int $id,
        int $idquery,
        string $description,
        string $params,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($id, $eventid, $occuredon, $correlationid, $causationid);
        $this->idquery = $idquery;
        $this->description = $description;
        $this->params = $params;
    }

    public static function event_name(): string
    {
        return "queryaction.created";
    }

    public static function from_primitives(
        int $aggregateId,
        array $body,
        ?string $eventId = null,
        ?int $occurredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    ): AbsEvent
    {
        return new self(
            $aggregateId,
            $body["id_query"],
            $body["description"],
            $body["params"],
            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
    {
        return [
            "id_query" => $this->idquery,
            "description" => $this->description,
            "params" => $this->params,
        ];
    }

    public function id_query(): int
    {
        return $this->idquery;
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