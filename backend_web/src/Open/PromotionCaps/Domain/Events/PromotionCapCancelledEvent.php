<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapCancelledEvent extends AbsEvent
{
    private int $idpromotion;
    private string $idtypeprev;
    private int $istest;

    public function __construct(
        int $idcapuser,

        int $idpromotion,
        string $idtypeprev,
        int $istest,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->idpromotion = $idpromotion;
        $this->idtypeprev = $idtypeprev;
        $this->istest = $istest;
    }

    public static function event_name(): string
    {
        return "promotioncap.cancelled";
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
            $body["id_type_prev"],
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
            "id_type_prev" => $this->idtypeprev,
            "is_test" => $this->istest,
        ];
    }

    public function id_promotion(): string
    {
        return $this->idpromotion;
    }

    public function id_type_prev(): string
    {
        return $this->idtypeprev;
    }

    public function is_test(): int
    {
        return $this->istest;
    }
}