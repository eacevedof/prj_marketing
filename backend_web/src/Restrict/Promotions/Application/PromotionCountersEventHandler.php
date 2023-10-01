<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\Events\{PromotionCapActionHasOccurredEvent, PromotionCapCancelledEvent};

final class PromotionCountersEventHandler extends AppService implements IEventSubscriber
{
    private function _increase_counters(IEvent $domevent): void
    {
        if (get_class($domevent) !== PromotionCapActionHasOccurredEvent::class) {
            return;
        }

        if ($domevent->idCapUser() &&
            RF::getInstanceOf(PromotionCapSubscriptionsRepository::class)->isTestModeByIdCapUser($domevent->idCapUser())
        ) {
            return;
        }

        $repopromo = RF::getInstanceOf(PromotionRepository::class);
        switch ($domevent->idType()) {
            case PromotionCapActionType::VIEWED:
                $repopromo->increaseViewedByPromotionId($domevent->idPromotion());
                break;
            case PromotionCapActionType::SUBSCRIBED:
                $repopromo->increaseSubscribedByPromotionId($domevent->idPromotion());
                break;
            case PromotionCapActionType::CONFIRMED:
                $repopromo->increaseConfirmedByPromotionId($domevent->idPromotion());
                break;
            case PromotionCapActionType::EXECUTED:
                $repopromo->increaseExecutedByPromotionId($domevent->idPromotion());
                break;
        }
    }

    private function _decrease_counters(IEvent $domevent): void
    {
        if (get_class($domevent) !== PromotionCapCancelledEvent::class) {
            return;
        }

        $repopromo = RF::getInstanceOf(PromotionRepository::class);
        switch ($domevent->idTypePrev()) {
            case PromotionCapActionType::SUBSCRIBED:
                $repopromo->decreaseSubscribedByPromotionId($domevent->idPromotion());
                break;
            case PromotionCapActionType::CONFIRMED:
                $repopromo->decreaseSubscribedByPromotionId($domevent->idPromotion());
                $repopromo->decreaseConfirmedByPromotionId($domevent->idPromotion());
                break;
        }
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if (!in_array(get_class($domainEvent), [PromotionCapActionHasOccurredEvent::class, PromotionCapCancelledEvent::class])) {
            return $this;
        }

        if ($domainEvent->isTestMode()) {
            return $this;
        }

        $this->_increase_counters($domainEvent);
        $this->_decrease_counters($domainEvent);

        return $this;
    }
}
