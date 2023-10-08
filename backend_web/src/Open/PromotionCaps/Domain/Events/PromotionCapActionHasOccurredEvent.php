<?php

namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class PromotionCapActionHasOccurredEvent extends AbstractEvent
{
    private int $idPromotion;
    private ?int $idCapUser;
    private int $idType;
    private string $urlRequest;
    private ?string $urlReferer;
    private string $remoteIp;
    private int $isTestMode;

    public function __construct(
        int     $idAggregate,
        int     $idPromotion,
        ?int    $idCapUser,
        int     $idType,
        string  $urlReq,
        ?string $urlRef,
        string  $remoteIp,
        int     $isTestMode,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idAggregate, $eventId, $occurredOn, $correlationId, $causationId);

        $this->idPromotion = $idPromotion;
        $this->idCapUser = $idCapUser;
        $this->idType = $idType;
        $this->urlRequest = $urlReq;
        $this->urlReferer = $urlRef;
        $this->remoteIp = $remoteIp;
        $this->isTestMode = $isTestMode;
    }

    public static function eventName(): string
    {
        return "promotioncap.action-occurred";
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
            $body["id_promouser"],
            $body["id_type"],
            $body["url_req"],
            $body["url_ref"],
            $body["remote_ip"],
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
            "id_promouser" => $this->idCapUser,
            "id_type" => $this->idType,
            "url_req" => $this->urlRequest,
            "url_ref" => $this->urlReferer,
            "remote_ip" => $this->remoteIp,
            "is_test" => $this->isTestMode,
        ];
    }

    public function idPromotion(): int
    {
        return $this->idPromotion;
    }

    public function idCapUser(): ?int
    {
        return $this->idCapUser;
    }

    public function idType(): int
    {
        return $this->idType;
    }

    public function urlRequest(): string
    {
        return $this->urlRequest;
    }

    public function urlReferer(): ?string
    {
        return $this->urlReferer;
    }

    public function remoteIp(): string
    {
        return $this->remoteIp;
    }

    public function isTestMode(): int
    {
        return $this->isTestMode;
    }
}
