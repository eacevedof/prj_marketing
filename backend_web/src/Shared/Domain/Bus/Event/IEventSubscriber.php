<?php
namespace App\Shared\Domain\Bus\Event;

interface IEventSubscriber
{
    public function on_event(IEvent $domevent): self;
}