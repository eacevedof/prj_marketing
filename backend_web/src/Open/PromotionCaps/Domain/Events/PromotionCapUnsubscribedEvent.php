<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapUnsubscribedEvent extends AbsEvent
{
    private string $subsuuid;
    private string $subsstatus;
    private string $dateconfirm;

    public function __construct(
        int $idcapuser,

        string $subsuuid,
        string $subsstatus,
        string $dateconfirm,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->subsuuid = $subsuuid;
        $this->subsstatus = $subsstatus;
        $this->dateconfirm = $dateconfirm;
    }

    public static function event_name(): string
    {
        return "promotioncap.unsubscribed";
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
            $body["date_confirm"],
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
            "date_confirm" => $this->dateconfirm,
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
    
    public function date_confirm(): string
    {
        return $this->dateconfirm;
    }
}