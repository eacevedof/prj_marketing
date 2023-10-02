<?php

namespace App\Open\PromotionCaps\Application;

use Exception;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Infrastructure\Traits\{LogTrait, RequestTrait};
use App\Restrict\Subscriptions\Domain\Events\SubscriptionExecutedEvent;
use App\Shared\Infrastructure\Components\Email\{FromTemplate, FuncEmailComponent};
use App\Shared\Infrastructure\Helpers\{QrHelper, RoutesHelper as Routes, UrlDomainHelper};
use App\Open\PromotionCaps\Domain\Events\{PromotionCapConfirmedEvent, PromotionCapUserSubscribedEvent};

use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, HelperFactory as HF, RepositoryFactory as RF};

final class PromotionSubscriptionNotifierEventHandler extends AppService implements IEventSubscriber
{
    use LogTrait;
    use RequestTrait;

    private UrlDomainHelper $domain;
    private string $lang;
    private array $emailTemplatesPaths;

    public function __construct()
    {
        //esto se hace en el listener no para cada llamada del evento
        $this->_loadRequestComponentInstance();
        $this->lang = $this->requestComponent->getLang();

        $this->domain = UrlDomainHelper::getInstance();
        $this->emailTemplatesPaths = [
            "subscription" => realpath(__DIR__."/../Infrastructure/Views/email/email-subscription.tpl"),
            "confirmation" => realpath(__DIR__."/../Infrastructure/Views/email/email-confirmation.tpl"),
            "execution" => realpath(__DIR__."/../Infrastructure/Views/email/email-execution.tpl"),
        ];
    }

    private function _onSubscription(IEvent $domainEvent): void
    {
        if(get_class($domainEvent) !== PromotionCapUserSubscribedEvent::class) {
            return;
        }

        $pathSubscriptionTemplate = $this->emailTemplatesPaths["subscription"];
        if (!is_file($pathSubscriptionTemplate)) {
            throw new Exception("Wrong path $pathSubscriptionTemplate");
        }

        $subscriptionEmail = RF::getInstanceOf(PromotionCapUsersRepository::class)->getSubscriptionData($domainEvent->aggregateId());

        $url = Routes::getUrlByRouteName("business.space", ["businessSlug" => $subscriptionEmail["businessslug"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $subscriptionEmail["space_link"] = $link;

        $url = Routes::getUrlByRouteName(
            "subscription.confirm",
            ["businessSlug" => $subscriptionEmail["businessslug"], "subscriptionUuid" => $subscriptionEmail["subscode"]]
        );
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $subscriptionEmail["confirm_link"] = $link;

        $url = Routes::getUrlByRouteName(
            "subscription.cancel",
            ["businessSlug" => $subscriptionEmail["businessslug"], "subscriptionUuid" => $subscriptionEmail["subscode"]]
        );
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $subscriptionEmail["unsubscribe_link"] = $link;

        $url = Routes::getUrlByRouteName("terms.by-promotion", ["promotionSlug" => $subscriptionEmail["promoslug"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $subscriptionEmail["terms_link"] = $link;

        $html = FromTemplate::getFileContent($pathSubscriptionTemplate, ["data" => $subscriptionEmail]);
        $this->logSql($html, "_on_subscription");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::getInstanceOf(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($subscriptionEmail["email"])
            ->set_subject(__("Subscription to “{0}“", $subscriptionEmail["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    private function _onSubscriptionConfirmation(IEvent $domainEvent): void
    {
        if(get_class($domainEvent) !== PromotionCapConfirmedEvent::class) {
            return;
        }

        $pathConfirmationTpl = $this->emailTemplatesPaths["confirmation"];
        if (!is_file($pathConfirmationTpl)) {
            throw new Exception("Wrong path $pathConfirmationTpl");
        }

        $data = RF::getInstanceOf(PromotionCapUsersRepository::class)->getSubscriptionData($domainEvent->aggregateId());

        $url = Routes::getUrlByRouteName("business.space", ["businessSlug" => $data["businessslug"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $data["space_link"] = $link;

        $url = Routes::getUrlByRouteName("user.points", ["businessSlug" => $data["businessslug"], "capuseruuid" => $data["capusercode"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $data["points_link"] = $link;

        $url = Routes::getUrlByRouteName("subscription.cancel", ["businessSlug" => $data["businessslug"], "subscriptionUuid" => $subscriptionUuid = $data["subscode"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $data["unsubscribe_link"] = $link;

        $url = Routes::getUrlByRouteName("terms.by-promotion", ["promotionSlug" => $data["promoslug"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $link .= $domainEvent->isTestMode() ? "?mode=test" : "";
        $data["terms_link"] = $link;

        $value = "$subscriptionUuid-{$data["execode"]}";
        $promotionExecutionDate = str_replace(["-",":"," "], ["","",""], $data["promodateexec"]);
        $filename = "$subscriptionUuid-{$promotionExecutionDate}";
        $link = HF::get(QrHelper::class, ["value" => $value, "filename" => $filename ])->saveImage()->getPublicUrl();
        //$link = "https://res.cloudinary.com/ioedu/image/upload/v1660317474/prj-marketing/partners/codigo-qr-ejemplo.png";
        $data["qr_link"] = $link;

        $html = FromTemplate::getFileContent($pathConfirmationTpl, ["data" => $data]);
        $this->logSql($html, "on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::getInstanceOf(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($data["email"])
            ->set_subject(__("Subscription to “{0}“", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    private function _onSubscriptionExecution(IEvent $domainEvent): void
    {
        if(get_class($domainEvent) !== SubscriptionExecutedEvent::class) {
            return;
        }

        $pathExecutionTpl = $this->emailTemplatesPaths["execution"];
        if (!is_file($pathExecutionTpl)) {
            throw new Exception("Wrong path $pathExecutionTpl");
        }

        $data = RF::getInstanceOf(PromotionCapUsersRepository::class)->getDataByPromotionCapSubscriptionUuid($domainEvent->uuid());

        $url = Routes::getUrlByRouteName("business.space", ["businessSlug" => $data["businessslug"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $data["space_link"] = $link;

        $url = Routes::getUrlByRouteName("user.points", ["businessSlug" => $data["businessslug"], "capuseruuid" => $data["capusercode"]]);
        $link = $this->domain->getDomainUrlWithAppend($url);
        $data["points_link"] = $link;
        $html = FromTemplate::getFileContent($pathExecutionTpl, ["data" => $data]);
        $this->logSql($html, "on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::getInstanceOf(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($data["email"])
            ->set_subject(__("Subscription to “{0}“", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
    }

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        $this->requestComponent->setLang("es");
        $this->_onSubscription($domainEvent);
        $this->_onSubscriptionConfirmation($domainEvent);
        $this->_onSubscriptionExecution($domainEvent);
        $this->requestComponent->setLang($this->lang);
        return $this;
    }
}
