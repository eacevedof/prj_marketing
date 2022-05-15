<?php
namespace App\Restrict\Subscriptions\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class SubscriptionExecutedEvent extends AbsEvent
{
    private string $uuid;
    private string $dateexecution;

    public function __construct(
        int $idsubscription,

        string $uuid,
        string $dateexecution,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idsubscription, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->dateexecution = $dateexecution;
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
            $body["uuid"], //app_promotioncap.uuid
            $body["date_execution"],
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
            "date_execution" => $this->dateexecution,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function date_execution(): string
    {
        return $this->dateexecution;
    }
}