<?php
namespace App\Open\Home\Domain\Events;

use App\Shared\Infrastructure\Bus\AbsEvent;

final class ContactEmailSentEvent extends AbsEvent
{
    private string $emailuuid;
    private string $email;
    private string $name;
    private string $subject;
    private string $message;

    public function __construct(
        int $idemail,

        string $emailuuid,
        string $email,
        string $name,
        string $subject,
        string $message,

        ?string $eventid = null,
        ?int $occuredon = null,
        ?string $correlationid = null,
        ?string $causationid = null
    )
    {
        parent::__construct($idemail, $eventid, $occuredon, $correlationid, $causationid);
        
        $this->emailuuid = $emailuuid;
        $this->email = $email;
        $this->name = $name;
        $this->subject = $subject;
        $this->message = $message;
    }

    public static function event_name(): string
    {
        return "contactemail.sent";
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
            $body["emailuuid"],
            $body["email"],
            $body["name"],
            $body["subject"],
            $body["message"],

            $eventId,
            $occurredon,
            $correlationid,
            $causationid
        );
    }

    public function to_primitives(): array
    {
        return [
            "emailuuid" => $this->emailuuid,
            "email" => $this->email,
            "name" => $this->name,
            "subject" => $this->subject,
            "message" => $this->message,
        ];
    }

    public function subscription_uuid(): string
    {
        return $this->emailuuid;
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