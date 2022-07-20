<?php
namespace App\Open\Home\Application;

use App\Open\Home\Domain\Events\ContactEmailSentEvent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Bus\Event\IEventSubscriber;
use App\Shared\Domain\Bus\Event\IEvent;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Email\FuncEmailComponent;
use App\Shared\Infrastructure\Components\Email\FromTemplate;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Shared\Infrastructure\Traits\LogTrait;
use \Exception;

final class ContactEmailSentEventHandler extends AppService implements IEventSubscriber
{
    use LogTrait;

    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==ContactEmailSentEvent::class) return $this;

        $pathtpl = "path-to-tpl-of-contact";
        if (!is_file($pathtpl)) throw new Exception("Wrong path $pathtpl");

        $data = RF::get(PromotionCapUsersRepository::class)->get_data_by_subsuuid($domevent->uuid());
        $link = "{$this->domain}/points/{$data["businesscode"]}/user/{$data["capusercode"]}";
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
            ->set_subject(__("Subscription to “{0}“", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
        return $this;
    }
}