<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Events\PromotionCapUserWasCreatedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Email\FuncEmailComponent;
use App\Shared\Infrastructure\Components\Email\FromTemplate;

final class PromotionSubscriptionNotifierEventHandler extends AppService implements IEventSubscriber
{

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==PromotionCapUserWasCreatedEvent::class) return $this;

        $path = __DIR__."/../Infrastructure/Views/email/subscription.tpl";
        $pathtpl = realpath($path);
        if (!is_file($pathtpl)) throw new \Exception("bad path $path");

        $html = FromTemplate::get_content($pathtpl, ["data"=>[
            "business" => "bb",
            "user" => "uu",
            "email" => "",
            "promotion" => "pppp",
            "promocode" => "ccod",
            "confirm_link" => "lllink",
        ]]);

        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from("eaf@yahoo.es")
            ->add_to("eaf@yahoo.es")
            ->set_subject(__("Promotion subscription {0}", "promo uuid"))
            ->set_content($html)
            ->send()
        ;
        return $this;
    }

    private function _get_promotion(PromotionCapUserWasCreatedEvent $event): array
    {
        $r = RF::get(PromotionCapSubscriptionsRepository::class)->get_confirm_email($event->id_promotion());
        return $r;
    }
}