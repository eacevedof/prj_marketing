<?php

namespace App\Open\Home\Domain\Events;

use App\Shared\Infrastructure\Bus\AbstractEvent;

final class ContactEmailSentEvent extends AbstractEvent
{
    private string $emailUuid;
    private string $email;
    private string $name;
    private string $subject;
    private string $message;

    public function __construct(
        int     $idEmail,
        string  $emailUuid,
        string  $email,
        string  $name,
        string  $subject,
        string  $message,
        ?string $eventId = null,
        ?int    $occurredOn = null,
        ?string $correlationId = null,
        ?string $causationId = null
    ) {
        parent::__construct($idEmail, $eventId, $occurredOn, $correlationId, $causationId);

        $this->emailUuid = $emailUuid;
        $this->email = $email;
        $this->name = $name;
        $this->subject = $subject;
        $this->message = $message;
    }

    public static function eventName(): string
    {
        return "contactemail.sent";
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
            $body["emailuuid"],
            $body["email"],
            $body["name"],
            $body["subject"],
            $body["message"],
            $eventId,
            $occurredOn,
            $correlationId,
            $causationId
        );
    }

    public function toPrimitives(): array
    {
        return [
            "emailuuid" => $this->emailUuid,
            "email" => $this->email,
            "name" => $this->name,
            "subject" => $this->subject,
            "message" => $this->message,
        ];
    }

    public function emailUuid(): string
    {
        return $this->emailUuid;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function message(): string
    {
        return $this->message;
    }
}
