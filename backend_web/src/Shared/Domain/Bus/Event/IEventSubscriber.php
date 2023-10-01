<?php

namespace App\Shared\Domain\Bus\Event;

interface IEventSubscriber
{
    public function onSubscribedEvent(IEvent $domainEvent): self;
}
