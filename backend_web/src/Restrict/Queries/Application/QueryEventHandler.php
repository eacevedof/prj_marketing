<?php
namespace App\Restrict\Queries\Application;

use App\Open\QueryCaps\Domain\Enums\QueryCapActionType;
use App\Restrict\Queries\Domain\Events\QueryActionWasCreatedEvent;
use App\Restrict\Queries\Domain\Events\QueryWasCreatedEvent;
use App\Restrict\Queries\Domain\QueryRepository;
use App\Restrict\Subscriptions\Domain\QueryCapSubscriptionsRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Open\QueryCaps\Domain\Events\QueryCapActionHasOccurredEvent;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;

final class QueryEventHandler extends AppService implements IEventSubscriber
{
    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if (get_class($domevent)!==QueryWasCreatedEvent::class) return $this;
        if (get_class($domevent)!==QueryActionWasCreatedEvent::class) return $this;

        if ($domevent->is_test()) return $this;

        //si se esta visualizando no llega idcapuser
        if ($domevent->id_capuser() && RF::get(QueryCapSubscriptionsRepository::class)->is_test_mode_by_id_capuser($domevent->id_capuser()))
            return $this;

        $repopromo = RF::get(QueryRepository::class);
        switch ($domevent->id_type()) {
            case QueryCapActionType::VIEWED:
                $repopromo->increase_viewed($domevent->id_promotion());
            break;
            case QueryCapActionType::SUBSCRIBED:
                $repopromo->increase_subscribed($domevent->id_promotion());
            break;
            case QueryCapActionType::CONFIRMED:
                $repopromo->increase_confirmed($domevent->id_promotion());
            break;
            case QueryCapActionType::EXECUTED:
                $repopromo->increase_executed($domevent->id_promotion());
            break;
        }

        return $this;
    }
}