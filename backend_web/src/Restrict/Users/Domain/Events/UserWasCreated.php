<?php
namespace App\Restrict\Users\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class UserWasCreated extends AbsEvent
{
    private string $uuid;
    private string $email;
    private int $idprofile;
    private ?int $idparent;

    public function __construct(
        int $iduser,
        string $uuid,
        string $email,
        int $idprofile,

        ?int $idparent = null,
        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($iduser, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->email = $email;
        $this->idprofile = $idprofile;
        $this->idparent = $idparent;
    }

    public static function event_name(): string
    {
        return "user.created";
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
            $body["id_profile"],
            $body["id_parent"],
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
            "id_parent" => $this->idparent,
            "id_profile" => $this->idprofile,
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

    public function id_parent(): int
    {
        return $this->idparent;
    }

    public function id_profile(): int
    {
        return $this->idprofile;
    }
}