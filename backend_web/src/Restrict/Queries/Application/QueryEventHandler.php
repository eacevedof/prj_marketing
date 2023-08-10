<?php

namespace App\Restrict\Queries\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Restrict\Queries\Domain\{QueryActionsRepository, QueryRepository};
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};
use App\Restrict\Queries\Domain\Events\{QueryActionWasCreatedEvent, QueryWasCreatedEvent};

final class QueryEventHandler extends AppService implements IEventSubscriber
{
    public function onCreated(object $domainEvent): void
    {
        if (get_class($domainEvent) !== QueryWasCreatedEvent::class) {
            return;
        }
        RF::getInstanceOf(QueryRepository::class)->insert([
            "uuid" => $domainEvent->uuid(),
            "description" => $domainEvent->description(),
            "query" => $domainEvent->query(),
            "total" => $domainEvent->total(),
            "module" => $domainEvent->module(),
            "insert_user" => SF::getAuthService()->getAuthUserArray()["id"] ?? -1,
        ]);
    }

    public function onAction(object $domainEvent): void
    {
        if (get_class($domainEvent) !== QueryActionWasCreatedEvent::class) {
            return;
        }
        RF::getInstanceOf(QueryActionsRepository::class)->insert([
            "id_query" => $domainEvent->idQuery(),
            "description" => $domainEvent->description(),
            "params" => $domainEvent->params(),
            "insert_user" => SF::getAuthService()->getAuthUserArray()["id"] ?? -1,
        ]);
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        $this->onCreated($domainEvent);
        $this->onAction($domainEvent);
        return $this;
    }
}
