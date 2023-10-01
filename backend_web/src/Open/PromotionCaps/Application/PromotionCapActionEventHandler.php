<?php

namespace App\Open\PromotionCaps\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Open\PromotionCaps\Domain\PromotionCapActionsRepository;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;

final class PromotionCapActionEventHandler extends AppService implements IEventSubscriber
{
    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== PromotionCapActionHasOccurredEvent::class) {
            return $this;
        }

        $action = [
            "id_promotion" => $domainEvent->idPromotion(),
            "id_promouser" => $domainEvent->idCapUser(),
            "id_type" => $domainEvent->idType(),
            "url_req" => $domainEvent->urlRequest(),
            "url_ref" => $domainEvent->urlReferer(),
            "remote_ip" => $domainEvent->remoteIp(),
            "is_test" => $domainEvent->isTestMode(),
        ];

        RF::getInstanceOf(PromotionCapActionsRepository::class)->insert($action);
        return $this;
    }
}
