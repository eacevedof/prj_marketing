<?php
namespace App\Restrict\Queries\Application;

use App\Open\QueryCaps\Domain\Enums\QueryCapActionType;
use App\Restrict\Queries\Domain\Events\QueryActionWasCreatedEvent;
use App\Restrict\Queries\Domain\Events\QueryWasCreatedEvent;
use App\Restrict\Queries\Domain\QueryRepository;
use App\Restrict\Queries\Domain\QueryActionsRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\QueryCaps\Domain\Events\QueryCapActionHasOccurredEvent;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;

final class QueryEventHandler extends AppService implements IEventSubscriber
{
    public function _on_created($domevent): void
    {
        if (get_class($domevent)!==QueryWasCreatedEvent::class) return;
        RF::get(QueryRepository::class)->insert([
            "uuid" => $domevent->uuid(),
            "description" => $domevent->description(),
            "query" => $domevent->query(),
            "total" => $domevent->total(),
            "module" => $domevent->module(),
            "insert_user" => SF::get_auth()->get_user()["id"] ?? -1,
        ]);
    }

    public function _on_action($domevent): void
    {
        if (get_class($domevent)!==QueryActionWasCreatedEvent::class) return;
        RF::get(QueryActionsRepository::class)->insert([
            "id_query" => $domevent->id_query(),
            "description" => $domevent->description(),
            "insert_user" => SF::get_auth()->get_user()["id"] ?? -1,
        ]);
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        $this->_on_created($domevent);
        $this->_on_action($domevent);
        return $this;
    }
}