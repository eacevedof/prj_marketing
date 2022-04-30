<?php
namespace App\Shared\Domain\Bus\Event;
use App\Shared\Domain\Bus\Event\IEvent;

interface IEventBus
{
    public function publish(IEvent ...$events): void;
}