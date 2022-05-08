<?php
namespace App\Restrict\Subscriptions\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class SubscriptionExecutedEvent extends AbsEvent
{
    private string $subsuuid;
    private string $email;
    private string $dateconfirm;

    public function __construct(
        int $idcapuser,

        string $subsuuid,
        string $email,
        string $dateconfirm,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->subsuuid = $subsuuid;
        $this->email = $email;
        $this->dateconfirm = $dateconfirm;
    }

    public static function event_name(): string
    {
        return "subscription.executed";
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
            $body["email"],
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
            "email" => $this->email,
            "date_confirm" => $this->dateconfirm,
        ];
    }

    public function subscription_uuid(): string
    {
        return $this->subsuuid;
    }

    public function email(): string
    {
        return $this->email;
    }
    
    public function date_confirm(): string
    {
        return $this->dateconfirm;
    }
}