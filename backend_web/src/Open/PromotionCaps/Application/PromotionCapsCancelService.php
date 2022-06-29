<?php
namespace App\Open\PromotionCaps\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Open\PromotionCaps\Domain\Events\PromotionCapCancelledEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\PromotionCapUsersEntity;
use App\Open\PromotionCaps\Domain\PromotionCapUsersRepository;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Traits\RequestTrait;

final class PromotionCapsCancelService extends AppService implements IEventDispatcher
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
        $this->input = $input;
        $this->istest = (int) $input["_test_mode"];
        $this->repopromocapsubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::get(PromotionCapUsersRepository::class);
        $this->repopromotion = RF::get(PromotionRepository::class);
    }

    private function _load_promotion(): void
    {
        $this->promotion = $this->repopromotion->get_by_uuid($this->input["promotionuuid"], ["date_to",]);
        if (!$this->promotion)
            $this->_promocap_exception(__("This promotion does not exist anymore"), ExceptionType::CODE_NOT_FOUND);

        $i = CF::get(DateComponent::class)->get_seconds_between(date("Y-m-d H:i:s"), $this->promotion["date_to"]);
        if ($i<=0)
            $this->_promocap_exception(__("This promotion has expired", ExceptionType::CODE_BAD_REQUEST));
    }

    private function _load_subscription(): void
    {
        $promosubscription = $this->repopromocapsubscription->get_by_uuid(
            $this->input["subscriptionuuid"],
            ["id_promouser", "subs_status", "delete_date"]
        );

        if (!$promosubscription || $promosubscription["delete_date"])
            $this->_promocap_exception(__("No subscription found"), ExceptionType::CODE_NOT_FOUND);

        if (in_array($promosubscription["subs_status"], [PromotionCapActionType::CANCELLED]))
            $this->_promocap_exception(__("Subscription not found"), ExceptionType::CODE_NOT_FOUND);

        $this->subscriptiondata = $this->repopromocapuser->get_subscription_data($promosubscription["id_promouser"]);
        if (!$this->subscriptiondata)
            $this->_promocap_exception(__("No subscription data found"), ExceptionType::CODE_NOT_FOUND);

        if ($this->subscriptiondata["promocode"]!==$this->promotion["uuid"])
            $this->_promocap_exception(__("Promotion code does not match for this subscription"), ExceptionType::CODE_BAD_REQUEST);

        if ($this->subscriptiondata["date_execution"])
            $this->_promocap_exception(__("You have already consumed your subscription"), ExceptionType::CODE_BAD_REQUEST);

        $this->subscriptiondata["subs_status"] = $promosubscription["subs_status"];
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _dispatch(): void
    {
        EventBus::instance()->publish(...[
            PromotionCapCancelledEvent::from_primitives($idcapuser = $this->subscriptiondata["idcapuser"], [
                "subsuuid" => $this->subscriptiondata["subscode"],
                "subs_status" => $this->subscriptiondata["subs_status"],
            ]),

            PromotionCapActionHasOccurredEvent::from_primitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $idcapuser,
                "id_type" => PromotionCapActionType::CANCELLED,
                "url_req" => $this->request->get_request_uri(),
                "url_ref" => $this->request->get_referer(),
                "remote_ip" => $this->request->get_remote_ip(),
                "is_test" => $this->istest,
            ])
        ]);
    }

    public function __invoke(): array
    {
        $this->_load_request();
        $this->_load_promotion();
        $this->_load_subscription();

        $this->repopromocapsubscription->set_model($entitysubs = MF::get(PromotionCapSubscriptionEntity::class));
        $cancel = [
            "id"=>$this->subscriptiondata["subsid"],
            "uuid"=>$this->subscriptiondata["subscode"],
            "subs_status" => PromotionCapActionType::CANCELLED
        ];
        $iduser = AuthService::getme()->get_user()["id"] ?? -1;
        $entitysubs->add_sysupdate($cancel, $iduser);
        $this->repopromocapsubscription->update($cancel);

        $cancel = [
            "id" => $this->subscriptiondata["idcapuser"],
            "uuid" => $this->subscriptiondata["capusercode"],

            "email" => "to-do"
        ];
        $this->repopromocapuser->set_model($entitysubs = MF::get(PromotionCapUsersEntity::class));
        $entitysubs->add_sysupdate($cancel, $iduser);
        $this->repopromocapuser->update($cancel);

        $this->_dispatch();

        return $this->subscriptiondata;
    }
}