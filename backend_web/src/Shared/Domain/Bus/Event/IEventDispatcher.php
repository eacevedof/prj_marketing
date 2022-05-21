<?php
namespace App\Shared\Domain\Bus\Event;

interface IEventDispatcher
{
    public function dispatch(): void;
}