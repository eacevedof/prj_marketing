<?php

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\Event\{
    IEvent,
    IEventBus,
    IEventSubscriber
};

final class EventBus implements IEventBus
{
    private array $subscribers;
    private static ?IEventBus $instance = null;
    private int $id = 0;

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self;
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
        $key = $this->id;
        $this->subscribers[$key] = $subscriber;
        $this->id++;
        return $key;
    }

    public function publish(IEvent ...$domainEvents): void
    {
        foreach ($domainEvents as $event) {
            foreach($this->subscribers as $subscriber) {
                $subscriber->onSubscribedEvent($event);
            }
        }
    }

    public function ofId(int $id): ?IEventSubscriber
    {
        return $this->subscribers[$id] ?? null;
    }

    public function unsubscribe(int $id): self
    {
        unset($this->subscribers[$id]);
        return $this;
    }
}
