<?php

namespace App\Open\PromotionCaps\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Open\PromotionCaps\Domain\Events\{PromotionCapActionHasOccurredEvent, PromotionCapCancelledEvent};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF};
use App\Open\PromotionCaps\Domain\{PromotionCapSubscriptionEntity, PromotionCapSubscriptionsRepository, PromotionCapUsersEntity, PromotionCapUsersRepository};

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
        $this->repopromocapsubscription = RF::getInstanceOf(PromotionCapSubscriptionsRepository::class);
        $this->repopromocapuser = RF::getInstanceOf(PromotionCapUsersRepository::class);
        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class);
    }

    private function _load_promotion(): void
    {
        $this->promotion = $this->repopromotion->getEntityByEntityUuid($this->input["promotionuuid"], ["id", "uuid", "date_to", "delete_date"]);
        if (!$this->promotion || $this->promotion["delete_date"]) {
            $this->_promocap_exception(__("This promotion does not exist anymore"), ExceptionType::CODE_NOT_FOUND);
        }

        $i = CF::getInstanceOf(DateComponent::class)->getSecondsBetween(date("Y-m-d H:i:s"), $this->promotion["date_to"]);
        if ($i <= 0) {
            $this->_promocap_exception(__("This promotion has expired", ExceptionType::CODE_BAD_REQUEST));
        }
    }

    private function _load_subscription(): void
    {
        $promosubscription = $this->repopromocapsubscription->getEntityByEntityUuid(
            $this->input["subscriptionuuid"],
            ["id_promouser", "subs_status", "delete_date"]
        );

        if (!$promosubscription || $promosubscription["delete_date"]) {
            $this->_promocap_exception(__("Subscription not found"), ExceptionType::CODE_NOT_FOUND);
        }

        if (in_array($promosubscription["subs_status"], [PromotionCapActionType::CANCELLED])) {
            $this->_promocap_exception(__("Subscription not found"), ExceptionType::CODE_NOT_FOUND);
        }

        $this->subscriptiondata = $this->repopromocapuser->getSubscriptionData($promosubscription["id_promouser"]);
        if (!$this->subscriptiondata) {
            $this->_promocap_exception(__("Subscription data not found"), ExceptionType::CODE_NOT_FOUND);
        }

        if ($this->subscriptiondata["promocode"] !== $this->promotion["uuid"]) {
            $this->_promocap_exception(__("Promotion code does not match for this subscription"), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($this->subscriptiondata["date_execution"]) {
            $this->_promocap_exception(__("You have already consumed your subscription"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->subscriptiondata["subs_status"] = $promosubscription["subs_status"];
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _dispatchEvents(): void
    {
        EventBus::instance()->publish(...[
            PromotionCapCancelledEvent::fromPrimitives($this->subscriptiondata["subsid"], [
                "id_promotion" => $this->promotion["id"],
                "id_type_prev" => $this->subscriptiondata["subs_status"],
                "is_test" => $this->istest,
            ]),

            PromotionCapActionHasOccurredEvent::fromPrimitives(-1, [
                "id_promotion" => $this->promotion["id"],
                "id_promouser" => $this->subscriptiondata["idcapuser"],
                "id_type" => PromotionCapActionType::CANCELLED,
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
        $cancel = [
            "id" => $this->subscriptiondata["subsid"],
            "uuid" => $this->subscriptiondata["subscode"],
            "subs_status" => PromotionCapActionType::CANCELLED
        ];
        $idUser = AuthService::getInstance()->getAuthUserArray()["id"] ?? -1;
        $entitysubs->addSysUpdate($cancel, $idUser);
        $this->repopromocapsubscription->update($cancel);

        $cancel = [
            "id" => $this->subscriptiondata["idcapuser"],
            "uuid" => $this->subscriptiondata["capusercode"],
            "email" => CF::getInstanceOf(TextComponent::class)->getCancelledEmail($this->subscriptiondata["email"]),
        ];
        $this->repopromocapuser->setAppEntity($entitysubs = MF::getInstanceOf(PromotionCapUsersEntity::class));
        $entitysubs->addSysUpdate($cancel, $idUser);
        $this->repopromocapuser->update($cancel);

        $this->_dispatchEvents();

        return $this->subscriptiondata;
    }
}
