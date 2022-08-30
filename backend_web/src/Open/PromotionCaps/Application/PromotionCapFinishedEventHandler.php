<?php
namespace App\Open\PromotionCaps\Application;

use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Subscriptions\Domain\Events\PromotionHasFinishedEvent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;

final class PromotionCapFinishedEventHandler extends AppService implements IEventSubscriber
{
    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==PromotionHasFinishedEvent::class) return $this;

        RF::get(PromotionCapSubscriptionsRepository::class)
            ->set_auth(AuthService::getme())
            ->mark_finished_by_id_promotion($domevent->aggregate_id());
        return $this;
    }
}