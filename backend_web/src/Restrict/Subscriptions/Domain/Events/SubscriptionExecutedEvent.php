<?php

namespace App\Restrict\Subscriptions\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class SubscriptionExecutedEvent extends AbstractEvent
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
    ) {
        parent::__construct($idsubscription, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->dateexecution = $dateexecution;
    }

    public static function eventName(): string
    {
        return "subscription.executed";
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
            $body["uuid"], //app_promotioncap.uuid
            $body["date_execution"],
            $eventId,
            $occurredOn,
            $correlationId,
            $causationId
        );
    }

    public function toPrimitives(): array
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
