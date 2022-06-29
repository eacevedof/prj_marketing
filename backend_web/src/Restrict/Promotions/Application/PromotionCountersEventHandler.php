<?php
namespace App\Restrict\Promotions\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Open\PromotionCaps\Domain\Events\PromotionCapCancelledEvent;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;

final class PromotionCountersEventHandler extends AppService implements IEventSubscriber
{
    private function _increase_counters(IEvent $domevent): void
    {
        $repopromo = RF::get(PromotionRepository::class);
        switch ($domevent->id_type()) {
            case PromotionCapActionType::VIEWED:
                $repopromo->increase_viewed($domevent->id_promotion());
            break;
            case PromotionCapActionType::SUBSCRIBED:
                $repopromo->increase_subscribed($domevent->id_promotion());
            break;
            case PromotionCapActionType::CONFIRMED:
                $repopromo->increase_confirmed($domevent->id_promotion());
            break;
            case PromotionCapActionType::EXECUTED:
                $repopromo->increase_executed($domevent->id_promotion());
            break;
        }
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if (get_class($domevent)!==PromotionCapActionHasOccurredEvent::class) return $this;

        if ($domevent->is_test()) return $this;

        //si se esta visualizando no llega idcapuser
        if ($domevent->id_capuser() && RF::get(PromotionCapSubscriptionsRepository::class)->is_test_mode_by_id_capuser($domevent->id_capuser()))
            return $this;

        $this->_increase_counters($domevent);

        return $this;
    }
}