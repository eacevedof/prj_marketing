<?php

namespace App\Restrict\Users\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class UserWasCreatedEvent extends AbstractEvent
{
    private string $uuid;
    private string $email;
    private int $idProfile;
    private ?int $idParent;

    public function __construct(
        int     $idUser,
        string  $uuid,
        string  $email,
        int     $idProfile,
        ?int    $idParent = null,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idUser, $eventId, $occurredOn, $correlationId, $causationId);
        $this->uuid = $uuid;
        $this->email = $email;
        $this->idProfile = $idProfile;
        $this->idParent = $idParent;
    }

    public static function eventName(): string
    {
        return "user.created";
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
            $body["id_profile"],
            $body["id_parent"],
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
            "id_parent" => $this->idParent,
            "id_profile" => $this->idProfile,
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

    public function idParent(): int
    {
        return $this->idParent;
    }

    public function idProfile(): int
    {
        return $this->idProfile;
    }
}
