<?php
namespace App\Open\PromotionCaps\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionWasExecutedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapActionsRepository;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;

final class PromotionCapActionEventHandler extends AppService implements IEventSubscriber
{
    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==PromotionCapActionWasExecutedEvent::class) return $this;

        $action = [
            "id_promotion" => $domevent->id_promotion(),
            "id_promouser" => $domevent->id_capuser(),
            "id_type" => $domevent->id_type(),
            "url_req" => $domevent->url_req(),
            "url_ref" => $domevent->url_ref(),
            "remote_ip" => $domevent->remote_ip()
        ];

        RF::get(PromotionCapActionsRepository::class)->insert($action);
        return $this;
    }
}