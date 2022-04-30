<?php
namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\Event\IEventBus;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
//use App\Shared\Infrastructure\Repositories\EventStoreRepository;

final class EventBus implements IEventBus
{
    private array $subscribers;
    private static ?IEventBus $instance = null;
    private int $id = 0;

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->subscribers = [];
    }

    public function __clone()
    {
        throw new \BadMethodCallException("Clone is not supported");
    }

    public function subscribe(IEventSubscriber $subscriber): int
    {
        $id = $this->id;
        $this->subscribers[$id] = $subscriber;
        $this->id++;
        return $id;
    }

    public function publish(IEvent ...$domainEvents): void
    {
        foreach ($domainEvents as $event) {
            //(new EventStoreRepository())->append($event);
            foreach($this->subscribers as $subscriber) {
                $subscriber->onDomainEvent($event);
            }
        }
    }

    public function of_id(int $id): ?IEventSubscriber
    {
        return $this->subscribers[$id] ?? null;
    }

    public function unsubscribe(int $id): self
    {
        unset($this->subscribers[$id]);
        return $this;
    }
}