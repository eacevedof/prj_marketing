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

        $link = $this->domain->get_full_url("{$data["promocode"]}/confirm/{$data["subscode"]}");
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["confirm_link"] = $link;

        $link = $this->domain->get_full_url("promotion/{$data["promocode"]}/cancel/{$data["subscode"]}");
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["unsubscribe_link"] = $link;

        $link = $this->domain->get_full_url("terms-and-conditions/{$data["promoslug"]}");
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["terms_link"] = $link;

        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"_on_subscription");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from("elchalanaua@gmail.com")
            //->add_to($data["email"])
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("Subscription to \"{0}\"", $data["promotion"]))
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
        $link = $this->domain->get_full_url("points/{$data["businesscode"]}/user/{$data["capusercode"]}");
        $data["points_link"] = $link;

        $link = $this->domain->get_full_url("promotion/{$data["promocode"]}/cancel/{$data["subscode"]}");
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["unsubscribe_link"] = $link;

        $link = $this->domain->get_full_url("terms-and-conditions/{$data["promoslug"]}");
        $link .= $domevent->is_test() ? "?mode=test" : "";
        $data["terms_link"] = $link;

        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from("elchalanaua@gmail.com")
            //->add_to($data["email"])
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("Subscription to \"{0}\"", $data["promotion"]))
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
        $link = $this->domain->get_full_url("points/{$data["businesscode"]}/user/{$data["capusercode"]}");
        $data["points_link"] = $link;
        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from("elchalanaua@gmail.com")
            //->add_to($data["email"])
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("Subscription to \"{0}\"", $data["promotion"]))
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