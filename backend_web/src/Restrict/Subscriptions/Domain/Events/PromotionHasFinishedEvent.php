<?php
namespace App\Restrict\Subscriptions\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionHasFinishedEvent extends AbsEvent
{
    private int $idsubscription;

    public function __construct(
        int $idpromotion,
        int $idsubscription,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idpromotion, $eventid, $occuredon, $correlationid, $causationid);
        $this->idsubscription = $idsubscription;
    }

    public static function event_name(): string
    {
        return "promotion.finished";
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
            $body["id"],
            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
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