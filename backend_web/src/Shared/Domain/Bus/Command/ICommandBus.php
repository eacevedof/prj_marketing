<?php
namespace App\Shared\Domain\Bus\Command;

interface ICommandBus
{
    public function subscribe(string $command, ICommandHandler $handler): void;

    public function publish(ICommand $command);
}