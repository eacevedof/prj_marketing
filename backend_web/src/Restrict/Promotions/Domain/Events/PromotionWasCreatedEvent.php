<?php

namespace App\Restrict\Promotions\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class PromotionWasCreatedEvent extends AbstractEvent
{
    private string $uuid;
    private int $idOwner;
    private string $slug;

    public function __construct(
        int     $idUser,
        string  $uuid,
        int     $idOwner,
        string  $slug,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idUser, $eventId, $occurredOn, $correlationId, $causationId);
        $this->uuid = $uuid;
        $this->idOwner = $idOwner;
        $this->slug = $slug;
    }

    public static function eventName(): string
    {
        return "promotion.created";
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
            $body["uuid"],
            $body["id_owner"],
            $body["slug"],
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
            "id_owner" => $this->idOwner,
            "slug" => $this->slug,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function idOwner(): int
    {
        return $this->idOwner;
    }

    public function slug(): string
    {
        return $this->slug;
    }
}
