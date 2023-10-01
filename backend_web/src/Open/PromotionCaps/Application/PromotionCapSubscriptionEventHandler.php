<?php

namespace App\Open\PromotionCaps\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Open\PromotionCaps\Domain\Events\PromotionCapUserSubscribedEvent;
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF};
use App\Open\PromotionCaps\Domain\{PromotionCapSubscriptionEntity, PromotionCapSubscriptionsRepository};

final class PromotionCapSubscriptionEventHandler extends AppService implements IEventSubscriber
{
    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== PromotionCapUserSubscribedEvent::class) {
            return $this;
        }

        $subscription = [
            "id_promouser" => $domainEvent->aggregateId(),
            "uuid" => "sb".uniqid(),
            "id_owner" => $domainEvent->idOwner(),
            "id_promotion" => $domainEvent->idPromotion(),
            "remote_ip" => $domainEvent->remoteIp(),
            "date_subscription" => $domainEvent->dateSubscription(),
            "code_execution" => "",
            "is_test" => $domainEvent->isTestMode(),
        ];

        $idUser = AuthService::getInstance()->getAuthUserArray()["id"] ?? -1;
        MF::getInstanceOf(PromotionCapSubscriptionEntity::class)->addSysInsert($subscription, $idUser);
        RF::getInstanceOf(PromotionCapSubscriptionsRepository::class)->insert($subscription);
        return $this;
    }
}
