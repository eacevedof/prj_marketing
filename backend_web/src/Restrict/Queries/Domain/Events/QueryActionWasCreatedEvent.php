<?php
namespace App\Restrict\Queries\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class QueryActionWasCreatedEvent extends AbsEvent
{
    private string $uuid;
    private int $idowner;
    private string $slug;

    public function __construct(
        int $iduser,
        string $uuid,
        int $idowner,
        string $slug,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($iduser, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->idowner = $idowner;
        $this->slug = $slug;
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
            $body["uuid"],
            $body["id_owner"],
            $body["slug"],
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
            "id_owner" => $this->idowner,
            "slug" => $this->slug,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function id_owner(): int
    {
        return $this->idowner;
    }

    public function slug(): string
    {
        return $this->slug;
    }
}