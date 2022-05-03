<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapUserWasCreatedEvent extends AbsEvent
{
    private string $uuid;
    private string $email;

    public function __construct(
        int $idcapuser,
        string $uuid,
        string $email,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->email = $email;
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
}