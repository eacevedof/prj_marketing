<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Events\PromotionCapConfirmedEvent;
use App\Open\PromotionCaps\Domain\Events\PromotionCapUserSubscribedEvent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Email\FuncEmailComponent;
use App\Shared\Infrastructure\Components\Email\FromTemplate;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Infrastructure\Traits\LogTrait;

final class PromotionSubscriptionNotifierEventHandler extends AppService implements IEventSubscriber
{
    use LogTrait;

    private function _on_subscription(IEvent $domevent): void
    {
        if(get_class($domevent)!==PromotionCapUserSubscribedEvent::class) return;

        $path = __DIR__."/../Infrastructure/Views/email/subscription-email.tpl";
        $pathtpl = realpath($path);
        if (!is_file($pathtpl)) throw new \Exception("bad path $path");

        $data = RF::get(PromotionCapUsersRepository::class)->get_subscription_data($domevent->aggregate_id());
        $data["confirm_link"] = "http://localhost:900/promotion/{$data["promocode"]}/confirm/{$data["subscode"]}";
        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"_on_subscription");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from("eaf@yahoo.es")
            //->add_to("eaf@yahoo.es")
            ->set_subject(__("Subscription to \"{0}\"", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    private function _on_confirmation(IEvent $domevent): void
    {
        if(get_class($domevent)!==PromotionCapConfirmedEvent::class) return;

        $path = __DIR__."/../Infrastructure/Views/email/confirmation-email.tpl";
        $pathtpl = realpath($path);
        if (!is_file($pathtpl)) throw new \Exception("bad path $path");

        $data = RF::get(PromotionCapUsersRepository::class)->get_subscription_data($domevent->aggregate_id());
        $data["points_link"] = "http://localhost:900/points/{$data["businesscode"]}/user/{$data["capusercode"]}";
        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from("eaf@yahoo.es")
            //->add_to("eaf@yahoo.es")
            ->set_subject(__("Subscription to \"{0}\"", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        $this->_on_subscription($domevent);
        $this->_on_confirmation($domevent);
        return $this;
    }
}