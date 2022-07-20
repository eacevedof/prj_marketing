<?php
namespace App\Open\Home\Application;

use App\Open\Home\Domain\Events\ContactEmailSentEvent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Email\FuncEmailComponent;
use App\Shared\Infrastructure\Components\Email\FromTemplate;
use App\Shared\Infrastructure\Helpers\UrlDomainHelper;
use App\Shared\Infrastructure\Traits\LogTrait;
use \Exception;

final class ContactEmailSentEventHandler extends AppService implements IEventSubscriber
{
    use LogTrait;

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==ContactEmailSentEvent::class) return $this;

        $pathtpl = realpath(__DIR__."/../Infrastructure/Views/email/email-contact.tpl");
        if (!is_file($pathtpl)) throw new Exception("Wrong path $pathtpl");

        $urldomain = UrlDomainHelper::get_instance();
        $data = [
            "business" => "ProviderXXX",
            "businessurl" => $urldomain->get_full_url(),
            "businesslogo" => $urldomain->get_full_url("themes/mypromos/images/provider-xxx-logo-orange.svg"),
            "name" => $domevent->name(),
            "message" => $domevent->message(),
            "urlfb" => "",
            "urltwitter" => "",
            "urlig" => "",
            "urltiktok" => "",
        ];
        $html = FromTemplate::get_content($pathtpl, ["data"=>$data]);
        $this->log($html,"on_confirmation");
        /**
         * @var FuncEmailComponent $email
         */
        $email = CF::get(FuncEmailComponent::class);
        $email
            ->set_from(getenv("APP_EMAIL_FROM1"))
            //->add_to($domevent->email())
            ->add_to("eacevedof@gmail.com")
            ->set_subject(__("{0} this is a copy of your message", $domevent->name()))
            ->set_content($html)
            ->send()
        ;
        return $this;
    }
}