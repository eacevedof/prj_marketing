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

    private string $domain;
    private array $tpls;

    public function __construct()
    {
        $this->domain = "https://".getenv("APP_DOMAIN");
        if (strstr($this->domain, "localhost"))
            $this->domain = str_replace("https://","http://", $this->domain);
        
        $this->tpls = [
            "subscription" => realpath(__DIR__."/../Infrastructure/Views/email/email-subscription.tpl"),
            "confirmation" => realpath(__DIR__."/../Infrastructure/Views/email/email-confirmation.tpl"),
            "execution" => realpath(__DIR__."/../Infrastructure/Views/email/email-execution.tpl"),
        ];
    }
    
    public function on_event(IEvent $domevent): IEventSubscriber
    {
        if(get_class($domevent)!==ContactEmailSentEvent::class) return;

        $pathtpl = $this->tpls["execution"];
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
            ->set_subject(__("Subscription to \"{0}\"", $data["promotion"]))
            ->set_content($html)
            ->send()
        ;
        return $this;
    }
}