<?php

namespace App\Open\PromotionCaps\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Subscriptions\Domain\Events\PromotionHasFinishedEvent;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;

final class PromotionCapFinishedEventHandler extends AppService implements IEventSubscriber
{
    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== PromotionHasFinishedEvent::class) {
            return $this;
        }

        RF::getInstanceOf(PromotionCapSubscriptionsRepository::class)
            ->setAuthService(AuthService::getInstance())
            ->markCapSubscriptionAsFinished($domainEvent->aggregateId());
        return $this;
    }
}
