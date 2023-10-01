<?php

namespace App\Restrict\Subscriptions\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class PromotionHasFinishedEvent extends AbstractEvent
{
    private int $idsubscription;

    public function __construct(
        int $idpromotion,
        int $idsubscription,
        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    ) {
        parent::__construct($idpromotion, $eventid, $occuredon, $correlationid, $causationid);
        $this->idsubscription = $idsubscription;
    }

    public static function eventName(): string
    {
        return "promotion.finished";
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
            $body["id"],
            $eventId,
            $occurredOn,
            $correlationId,
            $causationId
        );
    }

    public function toPrimitives(): array
    {
        return [
            "id" => $this->idsubscription,
        ];
    }

    public function get_id_subscription(): int
    {
        return $this->idsubscription;
    }
}
