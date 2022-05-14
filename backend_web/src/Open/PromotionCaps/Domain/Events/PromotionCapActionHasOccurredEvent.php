<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapActionHasOccurredEvent extends AbsEvent
{
    private int $idpromotion;
    private ?int $idcapuser;
    private int $idtype;
    private string $urlreq;
    private ?string $urlref;
    private string $remoteip;
    private int $istest;

    public function __construct(
        int $idaggregate,

        int $idpromotion,
        ?int $idcapuser,
        int $idtype,
        string $urlreq,
        ?string $urlref,
        string $remoteip,
        int $istest,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idaggregate, $eventid, $occuredon, $correlationid, $causationid);

        $this->idpromotion = $idpromotion;
        $this->idcapuser = $idcapuser;
        $this->idtype = $idtype;
        $this->urlreq = $urlreq;
        $this->urlref = $urlref;
        $this->remoteip = $remoteip;
        $this->istest = $istest;
    }

    public static function event_name(): string
    {
        return "promotioncapaction.created";
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
            $body["id_promotion"],
            $body["id_promouser"],
            $body["id_type"],
            $body["url_req"],
            $body["url_ref"],
            $body["remote_ip"],
            $body["is_test"],

            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
    {
        return [
            "id_promotion" => $this->idpromotion,
            "id_promouser" => $this->idcapuser,
            "id_type" => $this->idtype,
            "url_req" => $this->urlreq,
            "url_ref" => $this->urlref,
            "remote_ip" => $this->remoteip,
            "is_test" => $this->istest,
        ];
    }

    public function id_promotion(): int
    {
        return $this->idpromotion;
    }

    public function id_capuser(): ?int
    {
        return $this->idcapuser;
    }

    public function id_type(): int
    {
        return $this->idtype;
    }

    public function url_req(): string
    {
        return $this->urlreq;
    }

    public function url_ref(): ?string
    {
        return $this->urlref;
    }

    public function remote_ip(): string
    {
        return $this->remoteip;
    }

    public function is_test(): int
    {
        return $this->istest;
    }
}