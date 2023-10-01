<?php

namespace App\Open\Home\Application;

use Exception;
use App\Shared\Infrastructure\Traits\LogTrait;
use App\Shared\Infrastructure\Services\AppService;
use App\Open\Home\Domain\Events\ContactEmailSentEvent;
use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Shared\Domain\Bus\Event\{IEvent, IEventSubscriber};
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Email\{FromTemplate, FuncEmailComponent};

final class ContactEmailSentEventHandler extends AppService implements IEventSubscriber
{
    use LogTrait;

    public function onSubscribedEvent(IEvent $domainEvent): IEventSubscriber
    {
        if(get_class($domainEvent) !== ContactEmailSentEvent::class) {
            return $this;
        }

        $pathEmailTpl = realpath(__DIR__."/../Infrastructure/Views/email/email-contact.tpl");
        if (!is_file($pathEmailTpl)) {
            throw new Exception("Wrong path $pathEmailTpl");
        }

        $urlDomain = UrlDomainHelper::getInstance();
        $data = [
            "business" => "My Promos",
            "businessurl" => $urlDomain->getDomainUrlWithAppend(),
            "businesslogo" => $urlDomain->getDomainUrlWithAppend("themes/mypromos/images/mypromos-logo-orange.png"),
            "email" => $domainEvent->email(),
            "name" => $domainEvent->name(),
            "subject" => htmlentities($domainEvent->subject()),
            "message" => str_replace("\n", "<br/>", htmlentities($domainEvent->message())),
            "urlfb" => "",
            "urltwitter" => "",
            "urlig" => "",
            "urltiktok" => "",
        ];
        $html = FromTemplate::getFileContent($pathEmailTpl, ["data" => $data]);
        $this->logSql($html, "on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::getInstanceOf(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            ->add_to($domainEvent->email())
            ->add_bcc(getenv("APP_EMAIL_TO"))
            ->set_subject(__("{0} this is a copy of your message ", $domainEvent->name()))
            ->set_content($html)
            ->send()
        ;
        return $this;
    }
}
