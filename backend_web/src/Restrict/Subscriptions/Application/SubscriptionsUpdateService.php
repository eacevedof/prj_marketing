<?php
namespace App\Restrict\Subscriptions\Application;

use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
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
    private PromotionCapSubscriptionsRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $this->_map_input($input);

        if (!$this->input["_capuseruuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);
        if (!$this->input["exec_code"])
            $this->_exception(__("Empty voucher code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entitypromotion = MF::get(PromotionEntity::class);
        $this->validator = VF::get($this->input, $this->entitypromotion);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotion->set_model($this->entitypromotion);
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
            "_capuseruuid" => trim($input["capuseruuid"] ?? ""),
            "exec_code" => trim($input["exec_code"] ?? ""),
        ];
    }

    private function _check_entity_permission(array $promotion): void
    {
        if (!$this->repopromotion->get_id_by_uuid($uuid = $promotion["uuid"]))
            $this->_exception(
                __("{0} {1} does not exist", __("Promotion"), $uuid),
                ExceptionType::CODE_NOT_FOUND
            );

        if ($this->auth->is_system()) return;

        $idauthuser = (int) $this->authuser["id"];
        $identowner = (int) $promotion["id_owner"];
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

    private function _map_entity(array &$promotion): void
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

        $update = $this->entitypromotion->map_request($update);
        $this->_check_entity_permission($update);
        $this->_map_entity($update);
        $this->entitypromotion->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repopromotion->update($update);
        $promotion = $this->repopromotion->get_by_id($update["id"]);
        return [
            "affected" => $affected,
            "promotion" => [
                "id" => $promotion["id"],
                "uuid" => $promotion["uuid"],
                "is_launched" => $promotion["is_launched"],
                "slug" => $promotion["slug"],
                "is_published" => $promotion["is_published"],
            ]
        ];
    }
}