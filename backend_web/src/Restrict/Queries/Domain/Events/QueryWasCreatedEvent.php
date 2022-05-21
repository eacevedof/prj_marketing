<?php
namespace App\Restrict\Queries\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class QueryWasCreatedEvent extends AbsEvent
{
    private string $uuid;
    private string $description;
    private string $query;
    private string $module;
    
    public function __construct(
        int $id,
        string $uuid,
        string $description,
        string $query,
        string $module,
        
        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($id, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->description = $description;
        $this->query = $query;
        $this->module = $module;
    }

    public static function event_name(): string
    {
        return "query.created";
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
            $body["uuid"],
            $body["description"],
            $body["query"],
            $body["module"],
            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
    {
        return [
            "uuid" => $this->uuid,
            "description" => $this->description,
            "query" => $this->query,
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

    public function module(): string
    {
        return $this->module;
    }
}