<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapCancelledEvent extends AbsEvent
{
    private string $subsuuid;
    private string $subsstatus;

    public function __construct(
        int $idcapuser,

        string $subsuuid,
        string $subsstatus,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->subsuuid = $subsuuid;
        $this->subsstatus = $subsstatus;
    }

    public static function event_name(): string
    {
        return "promotioncap.cancelled";
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
            $body["subsuuid"],
            $body["subs_status"],
            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
    {
        return [
            "subsuuid" => $this->subsuuid,
            "subs_status" => $this->subsstatus,
        ];
    }

    public function subscription_uuid(): string
    {
        return $this->subsuuid;
    }

    public function subs_status(): string
    {
        return $this->subsstatus;
    }
}