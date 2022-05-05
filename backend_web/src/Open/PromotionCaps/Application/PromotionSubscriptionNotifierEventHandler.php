<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Events\PromotionCapUserWasCreatedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Restrict\Auth\Application\AuthService;
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

        $pathtpl = realpath(__DIR__."infrastructure/Views/email/subscription.tpl");
        $html = FromTemplate::get_content($pathtpl, ["data"=>[
            "business" => "",
            "user" => "",
            "promotion" => "",
            "promocode" => "",
            "confirm_link" => "",
        ]]);
        /**
         * @var FuncEmailComponent $email
         */
        //$email = CF::get(FuncEmailComponent::class);
        //$email = new FuncEmailComponent();
        $email
            ->set_from("eaf@yahoo.es")
            ->add_to("eaf@yahoo.es")
            ->set_subject(__("Promotion subscription {0}", "promo uuid"))
            ->set_content($html)
            ->send()
        ;


        $iduser = AuthService::getme()->get_user()["id"] ?? -1;
        MF::get(PromotionCapSubscriptionEntity::class)->add_sysinsert($subscription, $iduser);
        RF::get(PromotionCapSubscriptionsRepository::class)->insert($subscription);
        return $this;
    }
}