<?php

namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class PromotionCapUserSubscribedEvent extends AbstractEvent
{
    private string $uuid;
    private string $email;
    private int $idOwner;
    private int $idPromotion;
    private string $remoteIp;
    private string $dateSubscription;
    private int $isTestMode;

    public function __construct(
        int     $idCapUser,
        string  $uuid,
        string  $email,
        int     $idOwner,
        int     $idPromotion,
        string  $remoteIp,
        string  $dateSubscription,
        int     $isTestMode,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idCapUser, $eventId, $occurredOn, $correlationId, $causationId);
        $this->uuid = $uuid;
        $this->email = $email;
        $this->idOwner = $idOwner;
        $this->idPromotion = $idPromotion;
        $this->remoteIp = $remoteIp;
        $this->dateSubscription = $dateSubscription;
        $this->isTestMode = $isTestMode;
    }

    public static function eventName(): string
    {
        return "promotioncapuser.created";
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
            $body["email"],
            $body["id_owner"],
            $body["id_promotion"],
            $body["remote_ip"],
            $body["date_subscription"],
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
            "uuid" => $this->uuid,
            "email" => $this->email,
            "id_owner" => $this->idOwner,
            "id_promotion" => $this->idPromotion,
            "remote_ip" => $this->remoteIp,
            "date_subscription" => $this->dateSubscription,
            "is_test" => $this->isTestMode,
        ];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function idOwner(): int
    {
        return $this->idOwner;
    }

    public function idPromotion(): int
    {
        return $this->idPromotion;
    }

    public function remoteIp(): string
    {
        return $this->remoteIp;
    }

    public function dateSubscription(): string
    {
        return $this->dateSubscription;
    }

    public function isTestMode(): int
    {
        return $this->isTestMode;
    }
}
