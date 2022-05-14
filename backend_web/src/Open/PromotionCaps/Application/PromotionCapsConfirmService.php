<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Open\PromotionCaps\Domain\Events\PromotionCapConfirmedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;

final class PromotionCapsConfirmService extends AppService
{
    use RequestTrait;

    private PromotionRepository $repopromotion;
    private PromotionCapSubscriptionsRepository $repopromocapsubscription;
    private PromotionCapUsersRepository $repopromocapuser;

    private array $promotion;
    private array $subscriptiondata;
    private int $istest;

    public function __construct(array $input)
    {
        //"promotionuuid" => $promotionuuid,
        //"subscriptionuuid" => $subscriptionuuid
        $this->input = $input;
        $this->istest = (int) $input["_test_mode"];
        $this->repopromocapsubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
    }

    private function _load_promotion(): void
    {
        $this->promotion = $this->repopromotion->get_by_uuid($this->input["promotionuuid"], [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz",
            "date_from", "date_to", "id_owner"
        ]);

        SF::get(
            PromotionCapCheckService::class,
            [
                "email" => ($this->input["email"] ?? ""),
                "promotion" => $this->promotion,
            ]
        )
        ->is_suitable_or_fail();
    }

    private function _load_subscription(): void
    {
        $promosubscription = $this->repopromocapsubscription->get_by_uuid($this->input["subscriptionuuid"], ["id_promouser"]);
        if(!$promosubscription)
            $this->_promocap_exception(__("No subscription found"), ExceptionType::CODE_NOT_FOUND);

        $this->subscriptiondata = $this->repopromocapuser->get_subscription_data($promosubscription["id_promouser"]);
        if (!$this->subscriptiondata)
            $this->_promocap_exception(__("No subscription data found"), ExceptionType::CODE_NOT_FOUND);

        if ($this->subscriptiondata["promocode"]!==$this->promotion["uuid"])
            $this->_promocap_exception(__("Promotion code does not match for this subscription"), ExceptionType::CODE_BAD_REQUEST);

        if ($this->subscriptiondata["date_confirm"] || $this->subscriptiondata["date_execution"])
            $this->_promocap_exception(__("You have already confirmed your subscription"), ExceptionType::CODE_BAD_REQUEST);
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    public function __invoke(): array
    {
        $this->_load_request();
        $this->_load_promotion();
        $this->_load_subscription();
        $this->repopromocapsubscription->set_model($entitysubs = MF::get(PromotionCapSubscriptionEntity::class));
        $confirm = [
            "id"=>$this->subscriptiondata["subsid"],
            "uuid"=>$this->subscriptiondata["subscode"],
            "date_confirm"=> $date = date("Y-m-d H:i:s"),
            "code_execution" => CF::get(TextComponent::class)->get_random_word(4, 2),
            "subs_status" => PromotionCapActionType::CONFIRMED
        ];
        $iduser = AuthService::getme()->get_user()["id"] ?? -1;
        $entitysubs->add_sysupdate($confirm, $iduser);
        $this->repopromocapsubscription->update($confirm);

        EventBus::instance()->publish(...[
            PromotionCapConfirmedEvent::from_primitives($idcapuser = $this->subscriptiondata["idcapuser"], [
                "subsuuid" => $this->subscriptiondata["subscode"],
                "email" => $this->subscriptiondata["email"],
                "date_confirm" => $date,
            ]),

            PromotionCapActionHasOccurredEvent::from_primitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $idcapuser,
                "id_type" => PromotionCapActionType::CONFIRMED,
                "url_req" => $this->request->get_request_uri(),
                "url_ref" => $this->request->get_referer(),
                "remote_ip" => $this->request->get_remote_ip(),
                "is_test" => $this->istest,
            ])
        ]);

        return $this->subscriptiondata;
    }
}