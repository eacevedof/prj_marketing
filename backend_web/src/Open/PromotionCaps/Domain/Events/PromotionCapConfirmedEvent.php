<?php

namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class PromotionCapConfirmedEvent extends AbstractEvent
{
    private string $subscriptionUuid;
    private string $email;
    private string $dateConfirm;
    private int $isTestMode;

    public function __construct(
        int     $idCapUser,
        string  $subscriptionUuid,
        string  $email,
        string  $dateConfirm,
        int     $isTestMode,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idCapUser, $eventId, $occurredOn, $correlationId, $causationId);
        $this->subscriptionUuid = $subscriptionUuid;
        $this->email = $email;
        $this->dateConfirm = $dateConfirm;
        $this->isTestMode = $isTestMode;
    }

    public static function eventName(): string
    {
        return "promotioncap.confirmed";
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
            $body["subsuuid"],
            $body["email"],
            $body["date_confirm"],
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
            "subsuuid" => $this->subscriptionUuid,
            "email" => $this->email,
            "date_confirm" => $this->dateConfirm,
            "is_test" => $this->isTestMode,
        ];
    }

    public function subscriptionUuid(): string
    {
        return $this->subscriptionUuid;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function dateConfirm(): string
    {
        return $this->dateConfirm;
    }

    public function isTestMode(): int
    {
        return $this->isTestMode;
    }
}
