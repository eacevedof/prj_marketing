<?php
namespace App\Restrict\Subscriptions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Open\SubscriptionCaps\Domain\SubscriptionCapSubscriptionEntity;
use App\Restrict\Subscriptions\Domain\SubscriptionCapSubscriptionsRepository;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class SubscriptionsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private SubscriptionCapSubscriptionsRepository $reposubscription;
    private FieldsValidator $validator;
    private SubscriptionCapSubscriptionEntity $entitysubscription;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $this->_map_input($input);
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);
        if (!$this->input["exec_code"])
            $this->_exception(__("Empty voucher code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entitysubscription = MF::get(SubscriptionEntity::class);
        $this->validator = VF::get($this->input, $this->entitysubscription);
        $this->reposubscription = RF::get(SubscriptionRepository::class);
        $this->reposubscription->set_model($this->entitysubscription);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _map_input(array $input): array
    {
        return [
            "uuid" => trim($input["uuid"] ?? ""),
            "exec_code" => trim($input["exec_code"] ?? ""),
        ];
    }

    private function _check_entity_permission(array $subscription): void
    {
        if (!$this->reposubscription->get_id_by_uuid($uuid = $subscription["uuid"]))
            $this->_exception(
                __("{0} {1} does not exist", __("Subscription"), $uuid),
                ExceptionType::CODE_NOT_FOUND
            );

        if ($this->auth->is_system()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $subscription["id_owner"];
        //si el logado es propietario de la promocion
        if ($idauthuser===$identowner) return;
        //si el logado tiene el mismo owner que la promo
        if (RF::get(UserRepository::class)->get_idowner($idauthuser) === $identowner) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("exec_code", "exec_code", function ($data) {
                $code = $data["value"];

            })
        ;
        return $this->validator;
    }

    private function _map_entity(array &$subscription): void
    {
    }

    public function __invoke(): array
    {
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitysubscription->map_request($update);
        $this->_check_entity_permission($update);
        $this->_map_entity($update);
        $this->entitysubscription->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->reposubscription->update($update);
        $subscription = $this->reposubscription->get_by_id($update["id"]);
        return [
            "affected" => $affected,
            "promotion" => [
                "id" => $subscription["id"],
                "uuid" => $subscription["uuid"],
                "is_launched" => $subscription["is_launched"],
                "slug" => $subscription["slug"],
                "is_published" => $subscription["is_published"],
            ]
        ];
    }
}