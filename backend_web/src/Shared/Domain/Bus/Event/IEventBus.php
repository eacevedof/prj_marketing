<?php
namespace App\Shared\Domain\Bus\Event;

interface IEventBus
{
    public function publish(DomainEvent ...$events): void;
}