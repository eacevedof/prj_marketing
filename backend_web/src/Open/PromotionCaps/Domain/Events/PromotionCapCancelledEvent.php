<?php

namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class PromotionCapCancelledEvent extends AbstractEvent
{
    private int $idPromotion;
    private string $idTypePrev;
    private int $isTestMode;

    public function __construct(
        int     $idCapUser,
        int     $idPromotion,
        string  $idTypePrev,
        int     $isTestMode,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idCapUser, $eventId, $occurredOn, $correlationId, $causationId);
        $this->idPromotion = $idPromotion;
        $this->idTypePrev = $idTypePrev;
        $this->isTestMode = $isTestMode;
    }

    public static function eventName(): string
    {
        return "promotioncap.cancelled";
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
            $body["id_promotion"],
            $body["id_type_prev"],
            $body["is_test"],
            $eventId,
            $occurredOn,
            $correlationId,
            $causationId
        );
    }

    public function toPrimitives(): array
    {
        return [
            "id_promotion" => $this->idPromotion,
            "id_type_prev" => $this->idTypePrev,
            "is_test" => $this->isTestMode,
        ];
    }

    public function idPromotion(): string
    {
        return $this->idPromotion;
    }

    public function idTypePrev(): string
    {
        return $this->idTypePrev;
    }

    public function isTestMode(): int
    {
        return $this->isTestMode;
    }
}
