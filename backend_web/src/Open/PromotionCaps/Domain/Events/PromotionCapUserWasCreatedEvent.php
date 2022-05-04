<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapUserWasCreatedEvent extends AbsEvent
{
    private string $uuid;
    private string $email;
    private int $idowner;
    private int $idpromotion;

    public function __construct(
        int $idcapuser,
        string $uuid,
        string $email,
        int $idowner,
        int $idpromotion,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->email = $email;
        $this->idowner = $idowner;
        $this->idpromotion = $idpromotion;
    }

    public static function event_name(): string
    {
        return "promotioncapuser.created";
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
            $body["email"],
            $body["idowner"],
            $body["idpromotion"],
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
            "email" => $this->email,
            "idowner" => $this->idowner,
            "idpromotion" => $this->idpromotion,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function id_owner(): int
    {
        return $this->idowner;
    }

    public function id_promotion(): int
    {
        return $this->idpromotion;
    }
}