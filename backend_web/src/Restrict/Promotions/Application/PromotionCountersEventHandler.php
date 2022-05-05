<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionWasExecutedEvent;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;

final class PromotionCountersEventHandler extends AppService implements IEventSubscriber
{
    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==PromotionCapActionWasExecutedEvent::class) return $this;

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

        return $this;
    }
}