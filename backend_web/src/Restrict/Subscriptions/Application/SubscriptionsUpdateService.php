<?php
namespace App\Restrict\Subscriptions\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Restrict\BusinessData\Application\BusinessDataDisabledService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Subscriptions\Domain\Events\SubscriptionExecutedEvent;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Subscriptions\Domain\Events\PromotionHasFinishedEvent;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Open\PromotionCaps\Domain\Events\PromotionCapActionHasOccurredEvent;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class SubscriptionsUpdateService extends AppService implements IEventDispatcher
{
    use RequestTrait;
    private array $authuser;
    private PromotionCapSubscriptionsRepository $reposubscription;
    private PromotionCapSubscriptionEntity $entitysubscription;
    private array $dbsubscription;

    public function __construct(array $input)
    {
        if (!$input) 
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if(!SF::get_auth()->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );

        $this->_load_input($input);

        $this->entitysubscription = MF::get(PromotionCapSubscriptionEntity::class);
        $this->reposubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->reposubscription->set_model($this->entitysubscription);

        $this->authuser = SF::get_auth()->get_user();
    }

    private function _load_input(array $input): void
    {
        $this->input =  [
            "uuid" => trim($input["uuid"] ?? ""),
            "exec_code" => trim($input["exec_code"] ?? ""),
            "notes" => trim($input["notes"] ?? ""),
        ];

        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);
        if (!$this->input["exec_code"])
            $this->_exception(__("Empty voucher code"),ExceptionType::CODE_BAD_REQUEST);
    }

    private function _check_entity_permission(): void
    {
        if (SF::get_auth()->is_system()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $this->dbsubscription["id_owner"];
        //si el logado es propietario de la suscripcion
        if ($idauthuser===$identowner) return;
        //si el logado tiene el mismo owner que la suscripcion
        if (RF::get(UserRepository::class)->get_idowner($idauthuser) === $identowner) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _load_dbsubscription(): void
    {
        $this->dbsubscription = $this->reposubscription->get_by_uuid(
            $uuid = $this->input["uuid"],
            [
                "id", "id_owner", "code_execution", "date_confirm", "date_execution", "subs_status", "id_promotion",
                "delete_date"
            ]
        );

        if (!$this->dbsubscription || $this->dbsubscription["delete_date"])
            $this->_exception(
                __("{0} {1} does not exist", __("Subscription"), $uuid),
                ExceptionType::CODE_NOT_FOUND
            );

        if (SF::get(BusinessDataDisabledService::class)($this->dbsubscription["id_owner"]))
            $this->_promocap_exception(__("Business account disabled"));

        $promotion = RF::get(PromotionRepository::class)->get_by_id($this->dbsubscription["id_promotion"], ["disabled_date", "disabled_reason"]);
        if ($promotion["disabled_date"])
            $this->_promocap_exception(__("Promotion disabled. {0}", $promotion["disabled_reason"]), ExceptionType::CODE_LOCKED);
    }

    private function _check_promotion(): void
    {
        $promotion = RF::get(PromotionRepository::class)->get_by_id(
            $idpromotion = $this->dbsubscription["id_promotion"],
            ["date_execution", "id_tz"]
        );

        $utc = CF::get(UtcComponent::class);
        $dt = CF::get(DateComponent::class);

        $seconds = $dt->get_seconds_between($utc->get_nowdt_in_timezone(), $promotion["date_execution"]);
        if ($seconds<0) {
            EventBus::instance()->publish(...[PromotionHasFinishedEvent::from_primitives($idpromotion, $this->dbsubscription)]);
            $this->_promocap_exception(
                __("Sorry but this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }
    }

    private function _add_rules(): FieldsValidator
    {
        $validator = VF::get($this->input, $this->entitysubscription);
        $validator
            ->add_skip("exec_code")
            ->add_rule("exec_code", "exec_code", function ($data) {
                $code = $data["value"];
                $subscription = $this->dbsubscription;
                if (!$subscription["date_confirm"]) return __("Subscription not confirmed");
                if ($subscription["date_execution"]) return __("Voucher already validated");
                if ($subscription["subs_status"] === PromotionCapActionType::CANCELLED)
                    return __("Subscription cancelled");
                if ($subscription["subs_status"] === PromotionCapActionType::FINISHED)
                    return __("Promotion has finished");
                if ($subscription["code_execution"] !== $code)
                    return __("Invalid code");
                return false;
            })
        ;
        return $validator;
    }

    private function _dispatch(array $payload): array
    {
        $this->_load_request();

        $subscription = $payload["subscription"];

        EventBus::instance()->publish(...[
            SubscriptionExecutedEvent::from_primitives($subscription["id"], $subscription),
            PromotionCapActionHasOccurredEvent::from_primitives(-1, [
                "id_promotion" => $subscription["id_promotion"],
                "id_promouser" => $subscription["id_promouser"],
                "id_type" => PromotionCapActionType::EXECUTED,
                "url_req" => $this->request->get_request_uri(),
                "url_ref" => $this->request->get_referer(),
                "remote_ip" => $this->request->get_remote_ip(),
                "is_test" => $subscription["is_test"],
            ])
        ]);
    }

    public function __invoke(): array
    {
        $this->_load_dbsubscription();
        $this->_check_promotion();

        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $this->_check_entity_permission();

        $subscription = [
            "id" => $this->dbsubscription["id"],
            "uuid" => $this->input["uuid"],
            "date_execution" => date("Y-m-d H:i:s"),
            "subs_status" => PromotionCapActionType::EXECUTED,
            "notes" => $this->input["notes"],
            "exec_user" => $this->authuser["id"],
        ];
        $this->entitysubscription->add_sysupdate($subscription, $this->authuser["id"]);

        $affected = $this->reposubscription->update($subscription);
        $subscription = $this->reposubscription->get_by_id(
            $subscription["id"],
            ["id", "uuid", "date_confirm", "date_execution", "subs_status", "id_promouser", "id_promotion", "is_test"]
        );

        $this->_dispatch(["subscription"=>$subscription]);

        return [
            "affected" => $affected,
            "subscription" => $subscription
        ];
    }
}