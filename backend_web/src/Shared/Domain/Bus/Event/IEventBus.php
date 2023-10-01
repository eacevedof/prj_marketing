<?php

namespace App\Shared\Domain\Bus\Event;

interface IEventBus
{
    public function publish(IEvent ...$events): void;
}
