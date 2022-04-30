<?php
namespace App\Shared\Infrastructure\Bus;
use App\Shared\Domain\Bus\Event\IEvent;
use \DateTimeImmutable;

//https://github.com/CodelyTV/php-ddd-example/blob/main/src/Shared/Domain/Bus/Event/DomainEvent.php
abstract class AbsEvent implements IEvent
{
    private int $aggregateid;
    private string $eventid;
    private int $occuredon;

    //id del evento inicial
    private string $correlationid;
    //id del padre
    private string $causationid;

    public function __construct(
        int $aggregateid,
        ?string $eventid=null,
        ?int $occuredon = null,
        ?string $correlationid=null,
        ?string $causationid=null
    )
    {
        $this->aggregateid = $aggregateid;
        $this->eventid = $eventid ?? uniqid();
        $this->occuredon = $occuredon ?? (new DateTimeImmutable())->getTimestamp();
        //creador original
        $this->correlationid = $correlationid ?? $this->eventid;
        //padre directo
        $this->causationid = $causationid ?? $this->correlationid;
    }

    abstract public static function from_primitives(
        int $aggregateId,
        array $body,
        string $eventId,
        int $occurredon,
        string $correlationid,
        string $causationid
    ): self;

    abstract public static function event_name(): string;

    abstract public function to_primitives(): array;

    public function aggregate_id(): int
    {
        return $this->aggregateId;
    }

    public function event_id(): string
    {
        return $this->eventid;
    }

    public function occurred_on(): int
    {
        return $this->occuredon;
    }

    public function correlation_id(): string
    {
        return $this->correlationid;
    }

    public function causation_id(): string
    {
        return $this->causationid;
    }
}