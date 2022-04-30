<?php
namespace App\Shared\Domain\Bus\Event;

interface IEvent
{
    public function occurred_on(): int;
}