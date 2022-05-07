<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Events\PromotionCapUserSubscribedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;

final class PromotionCapConfirmationEventHandler extends AppService implements IEventSubscriber
{
    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==PromotionCapUserSubscribedEvent::class) return $this;

        $capsubscription = [
            "id_promouser" => $domevent->aggregate_id(),
            "uuid" => uniqid(),
            "id_owner" => $domevent->id_owner(),
            "id_promotion" => $domevent->id_promotion(),
            "remote_ip" => $domevent->remote_ip(),
            "date_subscription" => $domevent->date_subscription(),
            "code_execution" => ""
        ];

        $iduser = AuthService::getme()->get_user()["id"] ?? -1;
        MF::get(PromotionCapSubscriptionEntity::class)->add_sysinsert($capsubscription, $iduser);
        RF::get(PromotionCapSubscriptionsRepository::class)->insert($capsubscription);
        return $this;
    }
}