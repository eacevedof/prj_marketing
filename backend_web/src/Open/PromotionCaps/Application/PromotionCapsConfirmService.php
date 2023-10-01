<?php

namespace App\Open\PromotionCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Open\PromotionCaps\Domain\Events\{PromotionCapActionHasOccurredEvent, PromotionCapConfirmedEvent};
use App\Open\PromotionCaps\Domain\{PromotionCapSubscriptionEntity, PromotionCapSubscriptionsRepository, PromotionCapUsersRepository};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionCapsConfirmService extends AppService implements IEventDispatcher
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
        $this->repopromocapsubscription = RF::getInstanceOf(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::getInstanceOf(PromotionCapUsersRepository::class);
        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class);
    }

    private function _load_promotion(): void
    {
        $this->promotion = $this->repopromotion->getEntityByEntityUuid($this->input["promotionuuid"], [
            "delete_date", "id", "uuid", "slug", "max_confirmed", "is_published", "is_launched", "id_tz",
            "date_from", "date_to", "date_execution", "id_owner", "num_confirmed", "disabled_date"
        ]);

        SF::getInstanceOf(PromotionCapCheckService::class, [
            "email" => ($this->input["email"] ?? ""),
            "promotion" => $this->promotion,
            "is_test" => $this->istest,
            "user" => AuthService::getInstance()->getAuthUserArray()
        ])->isPromotionSuitableOrFail();
    }

    private function _load_subscription(): void
    {
        $promosubscription = $this->repopromocapsubscription->getEntityByEntityUuid(
            $this->input["subscriptionuuid"],
            ["id_promouser", "delete_date", "subs_status"]
        );

        if (!$promosubscription || $promosubscription["delete_date"]) {
            $this->_promocap_exception(__("Subscription not found"), ExceptionType::CODE_NOT_FOUND);
        }

        if (in_array($promosubscription["subs_status"], [PromotionCapActionType::CANCELLED])) {
            $this->_promocap_exception(__("Subscription cancelled"), ExceptionType::CODE_NOT_FOUND);
        }

        if (in_array($promosubscription["subs_status"], [PromotionCapActionType::CONFIRMED])) {
            $this->_promocap_exception(__("Subscription already confirmed"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->subscriptiondata = $this->repopromocapuser->getSubscriptionData($promosubscription["id_promouser"]);
        if (!$this->subscriptiondata) {
            $this->_promocap_exception(__("Subscription data not found"), ExceptionType::CODE_NOT_FOUND);
        }

        if ($this->subscriptiondata["promocode"] !== $this->promotion["uuid"]) {
            $this->_promocap_exception(__("Promotion code does not match for this subscription"), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($this->subscriptiondata["date_confirm"] || $this->subscriptiondata["date_execution"]) {
            $this->_promocap_exception(__("You have already confirmed your subscription"), ExceptionType::CODE_BAD_REQUEST);
        }
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _dispatchEvents(array $payload): void
    {
        EventBus::instance()->publish(...[
            PromotionCapConfirmedEvent::fromPrimitives($idcapuser = $this->subscriptiondata["idcapuser"], [
                "subsuuid" => $this->subscriptiondata["subscode"],
                "email" => $this->subscriptiondata["email"],
                "date_confirm" => $payload["date_confirm"],
                "is_test" => $this->istest,
            ]),

            PromotionCapActionHasOccurredEvent::fromPrimitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $idcapuser,
                "id_type" => PromotionCapActionType::CONFIRMED,
                "url_req" => $this->requestComponent->getRequestUri(),
                "url_ref" => $this->requestComponent->getReferer(),
                "remote_ip" => $this->requestComponent->getRemoteIp(),
                "is_test" => $this->istest,
            ])
        ]);
    }

    public function __invoke(): array
    {
        $this->_loadRequestComponentInstance();
        $this->_load_promotion();
        $this->_load_subscription();
        $this->repopromocapsubscription->setAppEntity($entitysubs = MF::getInstanceOf(PromotionCapSubscriptionEntity::class));
        $confirm = [
            "id" => $this->subscriptiondata["subsid"],
            "uuid" => $this->subscriptiondata["subscode"],
            "date_confirm" => $date = date("Y-m-d H:i:s"),
            "code_execution" => CF::getInstanceOf(TextComponent::class)->getRandomWord(4, 2),
            "subs_status" => PromotionCapActionType::CONFIRMED
        ];
        $idUser = AuthService::getInstance()->getAuthUserArray()["id"] ?? -1;
        $entitysubs->addSysUpdate($confirm, $idUser);
        $this->repopromocapsubscription->update($confirm);

        $this->_dispatchEvents(["date_confirm" => $date]);

        return $this->subscriptiondata;
    }
}
