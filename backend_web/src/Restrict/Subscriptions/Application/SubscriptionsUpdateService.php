<?php
namespace App\Restrict\Subscriptions\Application;

use App\Open\PromotionCaps\Domain\Enums\PromotionCapActionType;
use App\Open\PromotionCaps\Domain\PromotionCapSubscriptionEntity;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Subscriptions\Domain\Events\SubscriptionExecutedEvent;
use App\Restrict\Subscriptions\Domain\Events\SubscriptionFinishedEvent;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Shared\Infrastructure\Components\Date\DateComponent;
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class SubscriptionsUpdateService extends AppService
{
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

        $this->_map_input($input);

        $this->entitysubscription = MF::get(PromotionCapSubscriptionEntity::class);
        $this->reposubscription = RF::get(PromotionCapSubscriptionsRepository::class);
        $this->reposubscription->set_model($this->entitysubscription);

        $this->authuser = SF::get_auth()->get_user();
    }

    private function _map_input(array $input): void
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
        //si el logado es propietario de la promocion
        if ($idauthuser===$identowner) return;
        //si el logado tiene el mismo owner que la promo
        if (RF::get(UserRepository::class)->get_idowner($idauthuser) === $identowner) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _promocap_exception(string $message, int $code = ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        throw new PromotionCapException($message, $code);
    }

    private function _load_dbsubscription(): void
    {
        $this->dbsubscription = $this->reposubscription->get_by_uuid(
            $uuid = $this->input["uuid"],
            ["id", "uuid", "id_owner", "code_execution", "date_confirm", "date_execution", "subs_status", "id_promotion"]
        );

        $this->_exception(
            __("{0} {1} does not exist", __("Subscription"), $uuid),
            ExceptionType::CODE_NOT_FOUND
        );
    }

    private function _check_promotion(): void
    {
        $promotion = RF::get(PromotionRepository::class)->get_by_id(
            $this->dbsubscription["id_promotion"],
            ["date_to", "id_tz"]
        );
        $tz = RF::get(ArrayRepository::class)->get_timezone_description_by_id($promotion["id_tz"]);
        $utc = CF::get(UtcComponent::class);
        $dt = CF::get(DateComponent::class);

        $utcto = $utc->get_dt_into_tz($promotion["date_to"], $tz);
        $utcnow = $utc->get_dt_by_tz();
        $seconds = $dt->get_seconds_between($utcnow, $utcto);
        if($seconds<0) {
            //EventBus::instance()->publish(...[SubscriptionFinishedEvent::from_primitives($id, $subscription)]);
            $this->_promocap_exception(
                __("Sorry but you can not validate this voucher because this promotion has finished."),
                ExceptionType::CODE_UNAVAILABLE_FOR_LEGAL_REASONS
            );
        }
    }

    private function _add_rules(): FieldsValidator
    {
        $validator = VF::get($this->input, $this->entitysubscription);
        $validator
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
            "uuid" => $this->dbsubscription["uuid"],
            "date_execution" => date("Y-m-d H:i:s"),
            "subs_status" => PromotionCapActionType::EXECUTED,
            "exec_user" => $this->authuser["id"],
        ];
        $this->entitysubscription->add_sysupdate($subscription, $this->authuser["id"]);

        $affected = $this->reposubscription->update($subscription);
        $subscription = $this->reposubscription->get_by_id(
            $id = $subscription["id"],
            ["id", "date_confirm", "date_execution", "subs_status"]
        );
        /*
        EventBus::instance()->publish(...[
            SubscriptionExecutedEvent::from_primitives($id, $subscription)
        ]);
        */

        return [
            "affected" => $affected,
            "subscription" => $subscription
        ];
    }
}