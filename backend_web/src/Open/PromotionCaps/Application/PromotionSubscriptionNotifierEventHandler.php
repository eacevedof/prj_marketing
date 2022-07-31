<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Events\PromotionCapConfirmedEvent;
use App\Open\PromotionCaps\Domain\Events\PromotionCapUserSubscribedEvent;
use App\Restrict\Subscriptions\Domain\Events\SubscriptionExecutedEvent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Shared\Infrastructure\Components\Email\FuncEmailComponent;
use App\Shared\Infrastructure\Components\Email\FromTemplate;
use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Infrastructure\Traits\LogTrait;
use \Exception;

final class PromotionSubscriptionNotifierEventHandler extends AppService implements IEventSubscriber
{
    use LogTrait;

    private UrlDomainHelper $domain;
    private array $tpls;

    public function __construct()
    {
        $this->domain = UrlDomainHelper::get_instance();
        $this->tpls = [
            "subscription" => realpath(__DIR__."/../Infrastructure/Views/email/email-subscription.tpl"),
            "confirmation" => realpath(__DIR__."/../Infrastructure/Views/email/email-confirmation.tpl"),
            "execution" => realpath(__DIR__."/../Infrastructure/Views/email/email-execution.tpl"),
        ];
    }

    private function _on_subscription(IEvent $domevent): void
    {
        if(get_class($domevent)!==PromotionCapUserSubscribedEvent::class) return;

        $pathtpl = $this->tpls["subscription"];
        if (!is_file($pathtpl)) throw new Exception("Wrong path $pathtpl");

        $data = RF::get(PromotionCapUsersRepository::class)->get_subscription_data($domevent->aggregate_id());

        $url = Routes::url("subscription.confirm", ["promotionuuid"=>$data["promocode"], "subscriptionuuid"=>$data["subscode"]]);
        $link = $this->domain->get_full_url($url);
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["confirm_link"] = $link;

        $url = Routes::url("subscription.cancel", ["promotionuuid"=>$data["promocode"], "subscriptionuuid"=>$data["subscode"]]);
        $link = $this->domain->get_full_url($url);
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["unsubscribe_link"] = $link;

        $url = Routes::url("terms.by-promotion", ["promoslug"=>$data["promoslug"]]);
        $link = $this->domain->get_full_url($url);
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["terms_link"] = $link;

        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"_on_subscription");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($data["email"])
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("Subscription to “{0}“", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    private function _on_confirmation(IEvent $domevent): void
    {
        if(get_class($domevent)!==PromotionCapConfirmedEvent::class) return;

        $pathtpl = $this->tpls["confirmation"];
        if (!is_file($pathtpl)) throw new Exception("Wrong path $pathtpl");

        $data = RF::get(PromotionCapUsersRepository::class)->get_subscription_data($domevent->aggregate_id());

        $url = Routes::url("user.points", ["businessuuid"=>$data["businesscode"], "capuseruuid"=>$data["capusercode"]]);
        $link = $this->domain->get_full_url($url);
        $data["points_link"] = $link;

        $url = Routes::url("subscription.cancel", ["promotionuuid"=>$data["promocode"], "subscriptionuuid"=>$data["subscode"]]);
        $link = $this->domain->get_full_url($url);
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["unsubscribe_link"] = $link;

        $url = Routes::url("terms.by-promotion", ["promoslug"=>$data["promoslug"]]);
        $link = $this->domain->get_full_url($url);
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["terms_link"] = $link;

        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($data["email"])
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("Subscription to “{0}“", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    private function _on_execution(IEvent $domevent): void
    {
        if(get_class($domevent)!==SubscriptionExecutedEvent::class) return;

        $pathtpl = $this->tpls["execution"];
        if (!is_file($pathtpl)) throw new Exception("Wrong path $pathtpl");

        $data = RF::get(PromotionCapUsersRepository::class)->get_data_by_subsuuid($domevent->uuid());

        $url = Routes::url("user.points", ["businessuuid"=>$data["businesscode"], "capuseruuid"=>$data["capusercode"]]);
        $link = $this->domain->get_full_url($url);
        $data["points_link"] = $link;
        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($data["email"])
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("Subscription to “{0}“", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        $this->_on_subscription($domevent);
        $this->_on_confirmation($domevent);
        $this->_on_execution($domevent);
        return $this;
    }
}