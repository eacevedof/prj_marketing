<?php

namespace App\Restrict\Subscriptions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Restrict\BusinessData\Application\BusinessDataDisabledService;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Shared\Infrastructure\Components\Date\{DateComponent, UtcComponent};
use App\Restrict\Subscriptions\Domain\Events\{PromotionHasFinishedEvent, SubscriptionExecutedEvent};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class SubscriptionsUpdateService extends AppService implements IEventDispatcher
{
    use RequestTrait;
    private array $authUserArray;
    private PromotionCapSubscriptionsRepository $reposubscription;
    private PromotionCapSubscriptionEntity $entitysubscription;
    private array $dbsubscription;

    public function __construct(array $input)
    {
        if (!$input) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!SF::getAuthService()->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }

        $this->_load_input($input);

        $this->entitysubscription = MF::getInstanceOf(PromotionCapSubscriptionEntity::class);
        $this->reposubscription = RF::getInstanceOf(PromotionCapSubscriptionsRepository::class);
        $this->reposubscription->setAppEntity($this->entitysubscription);

        $this->authUserArray = SF::getAuthService()->getAuthUserArray();
    }

    private function _load_input(array $input): void
    {
        $this->input =  [
            "uuid" => trim($input["uuid"] ?? ""),
            "exec_code" => trim($input["exec_code"] ?? ""),
            "notes" => trim($input["notes"] ?? ""),
        ];

        if (!$this->input["uuid"]) {
            $this->_throwException(__("Empty required code"), ExceptionType::CODE_BAD_REQUEST);
        }
        if (!$this->input["exec_code"]) {
            $this->_throwException(__("Empty voucher code"), ExceptionType::CODE_BAD_REQUEST);
        }
    }

    private function _checkEntityPermissionOrFail(): void
    {
        if (SF::getAuthService()->hasAuthUserSystemProfile()) {
            return;
        }

        $idauthuser = (int) $this->authUserArray["id"];
        $identowner = (int) $this->dbsubscription["id_owner"];
        //si el logado es propietario de la suscripcion
        if ($idauthuser === $identowner) {
            return;
        }
        //si el logado tiene el mismo owner que la suscripcion
        if (RF::getInstanceOf(UserRepository::class)->getIdOwnerByIdUser($idauthuser) === $identowner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _load_dbsubscription(): void
    {
        $this->dbsubscription = $this->reposubscription->getEntityByEntityUuid(
            $uuid = $this->input["uuid"],
            [
                "id", "id_owner", "code_execution", "date_confirm", "date_execution", "subs_status", "id_promotion",
                "delete_date"
            ]
        );

        if (!$this->dbsubscription || $this->dbsubscription["delete_date"]) {
            $this->_throwException(
                __("{0} {1} does not exist", __("Subscription"), $uuid),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        if (SF::getInstanceOf(BusinessDataDisabledService::class)($this->dbsubscription["id_owner"])) {
            $this->_promocap_exception(__("Business account disabled"));
        }

        $promotion = RF::getInstanceOf(PromotionRepository::class)->getEntityByEntityId($this->dbsubscription["id_promotion"], ["disabled_date", "disabled_reason"]);
        if ($promotion["disabled_date"]) {
            $this->_promocap_exception(__("Promotion disabled. {0}", $promotion["disabled_reason"]), ExceptionType::CODE_LOCKED);
        }
    }

    private function _check_promotion(): void
    {
        $promotion = RF::getInstanceOf(PromotionRepository::class)->getEntityByEntityId(
            $idpromotion = $this->dbsubscription["id_promotion"],
            ["date_execution", "id_tz"]
        );

        $utc = CF::getInstanceOf(UtcComponent::class);
        $dt = CF::getInstanceOf(DateComponent::class);

        $seconds = $dt->getSecondsBetween($utc->getNowDtIntoTargetTz(), $promotion["date_execution"]);
        if ($seconds < 0) {
            EventBus::instance()->publish(...[PromotionHasFinishedEvent::fromPrimitives($idpromotion, $this->dbsubscription)]);
            $this->_promocap_exception(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }
    }

    private function _add_rules(): FieldsValidator
    {
        $validator = VF::getFieldValidator($this->input, $this->entitysubscription);
        $validator
            ->addSkipableField("exec_code")
            ->addRule("exec_code", "exec_code", function ($data) {
                $code = $data["value"];
                $subscription = $this->dbsubscription;
                if (!$subscription["date_confirm"]) {
                    return __("Subscription not confirmed");
                }
                if ($subscription["date_execution"]) {
                    return __("Voucher already validated");
                }
                if ($subscription["subs_status"] === PromotionCapActionType::CANCELLED) {
                    return __("Subscription cancelled");
                }
                if ($subscription["subs_status"] === PromotionCapActionType::FINISHED) {
                    return __("Promotion has finished");
                }
                if ($subscription["code_execution"] !== $code) {
                    return __("Invalid code");
                }
                return false;
            })
        ;
        return $validator;
    }

    private function _dispatchEvents(array $payload): void
    {
        $this->_loadRequestComponentInstance();

        $subscription = $payload["subscription"];

        EventBus::instance()->publish(...[
            SubscriptionExecutedEvent::fromPrimitives($subscription["id"], $subscription),
            PromotionCapActionHasOccurredEvent::fromPrimitives(-1, [
                "id_promotion" => $subscription["id_promotion"],
                "id_promouser" => $subscription["id_promouser"],
                "id_type" => PromotionCapActionType::EXECUTED,
                "url_req" => $this->requestComponent->getRequestUri(),
                "url_ref" => $this->requestComponent->getReferer(),
                "remote_ip" => $this->requestComponent->getRemoteIp(),
                "is_test" => $subscription["is_test"],
            ])
        ]);
    }

    public function __invoke(): array
    {
        $this->_load_dbsubscription();
        $this->_check_promotion();

        if ($errors = $this->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $this->_checkEntityPermissionOrFail();

        $subscription = [
            "id" => $this->dbsubscription["id"],
            "uuid" => $this->input["uuid"],
            "date_execution" => date("Y-m-d H:i:s"),
            "subs_status" => PromotionCapActionType::EXECUTED,
            "notes" => $this->input["notes"],
            "exec_user" => $this->authUserArray["id"],
        ];
        $this->entitysubscription->addSysUpdate($subscription, $this->authUserArray["id"]);

        $affected = $this->reposubscription->update($subscription);
        $subscription = $this->reposubscription->getEntityByEntityId(
            $subscription["id"],
            ["id", "uuid", "date_confirm", "date_execution", "subs_status", "id_promouser", "id_promotion", "is_test"]
        );

        $this->_dispatchEvents(["subscription" => $subscription]);

        return [
            "affected" => $affected,
            "subscription" => $subscription
        ];
    }
}
