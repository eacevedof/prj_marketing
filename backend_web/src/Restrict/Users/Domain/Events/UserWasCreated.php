<?php
namespace App\Restrict\Users\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class UserWasCreated extends AbsEvent
{
    private string $uuid;
    private string $email;
    private int $idparent;
    private int $idprofile;

    public function __construct(
        int $iduser,
        string $uuid,
        string $email,
        int $idparent,
        int $idprofile,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($iduser, $eventid, $occuredon, $correlationid, $causationid);
        $this->uuid = $uuid;
        $this->email = $email;
        $this->idparent = $idparent;
        $this->idprofile = $idprofile;
    }

    public static function event_name(): string
    {
        return "user.created";
    }

    public static function from_primitives(
        int $aggregateId,
        array $body,
        string $eventId,
        int $occurredon,
        string $correlationid,
        string $causationid
    ): AbsEvent
    {
        return new self(
            $aggregateId,
            $body["uuid"],
            $body["email"],
            $body["id_parent"],
            $body["id_profile"],
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