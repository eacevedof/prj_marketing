<?php
namespace App\Open\PromotionCaps\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class PromotionCapUserSubscribedEvent extends AbsEvent
{
    private string $uuid;
    private string $email;
    private int $idowner;
    private int $idpromotion;
    private string $remoteip;
    private string $datesubscription;

    public function __construct(
        int $idcapuser,

        string $uuid,
        string $email,
        int $idowner,
        int $idpromotion,
        string $remoteip,
        string $datesubscription,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idcapuser, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->email = $email;
        $this->idowner = $idowner;
        $this->idpromotion = $idpromotion;
        $this->remoteip = $remoteip;
        $this->datesubscription = $datesubscription;
    }

    public static function event_name(): string
    {
        return "promotioncapuser.created";
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
            $body["uuid"],
            $body["email"],
            $body["id_owner"],
            $body["id_promotion"],
            $body["remote_ip"],
            $body["date_subscription"],
            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
    {
        return [
            "uuid" => $this->uuid,
            "email" => $this->email,
            "id_owner" => $this->idowner,
            "id_promotion" => $this->idpromotion,
            "remote_ip" => $this->remoteip,
            "date_subscription" => $this->datesubscription,
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

    public function id_owner(): int
    {
        return $this->idowner;
    }

    public function id_promotion(): int
    {
        return $this->idpromotion;
    }

    public function remote_ip(): string
    {
        return $this->remoteip;
    }

    public function date_subscription(): string
    {
        return $this->datesubscription;
    }
}