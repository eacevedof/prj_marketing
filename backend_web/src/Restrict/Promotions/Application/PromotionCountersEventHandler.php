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
        if (get_class($domevent)!==PromotionCapActionHasOccurredEvent::class) return;

        if ($domevent->id_capuser() &&
            RF::get(PromotionCapSubscriptionsRepository::class)->is_test_mode_by_id_capuser($domevent->id_capuser())
        )
            return;

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

    private function _decrease_counters(IEvent $domevent): void
    {
        if (get_class($domevent)!==PromotionCapCancelledEvent::class) return;

        $repopromo = RF::get(PromotionRepository::class);
        switch ($domevent->id_type_prev()) {
            case PromotionCapActionType::SUBSCRIBED:
                $repopromo->decrease_subscribed($domevent->id_promotion());
            break;
            case PromotionCapActionType::CONFIRMED:
                $repopromo->decrease_subscribed($domevent->id_promotion());
                $repopromo->decrease_confirmed($domevent->id_promotion());
            break;
        }
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if (!in_array(get_class($domevent), [PromotionCapActionHasOccurredEvent::class, PromotionCapCancelledEvent::class]))
            return $this;

        if ($domevent->is_test()) return $this;

        $this->_increase_counters($domevent);
        $this->_decrease_counters($domevent);

        return $this;
    }
}