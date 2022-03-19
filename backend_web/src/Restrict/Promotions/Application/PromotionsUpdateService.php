<?php
namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionEntity;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class PromotionsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private PromotionRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entitypromotion = MF::get(PromotionEntity::class);
        $this->validator = VF::get($this->input, $this->entitypromotion);
        $this->repopromotion = RF::get(PromotionRepository::class);
        $this->repopromotion->set_model($this->entitypromotion);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $idpromotion = $this->repopromotion->get_id_by_uuid($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $idpromotion) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repopromotion->get_idowner($idpromotion);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_MANAGER])
            && $idauthuser === $identowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id", "id", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("uuid", "uuid", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_owner", "id_owner", function ($data) {
                if (!$value = $data["value"]) return __("Empty field is not allowed");
                if (!RF::get(UserRepository::class)->is_owner((int) $value))
                    return __("Invalid owner");
                return false;
            })
            ->add_rule("description", "description", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("date_from", "date_from", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("date_to", "date_to", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_tz", "id_tz", function ($data) {
                if (!$value = $data["value"]) return __("Empty field is not allowed");
                if (!RF::get(ArrayRepository::class)->get_timezone_description_by_id($value))
                    return __("Invalid timezone");
                return false;
            })
        ;
        return $this->validator;
    }

    private function _map_entity(array &$entity): void
    {
        $utc = CF::get(UtcComponent::class);
        $tzfrom = RF::get(ArrayRepository::class)->get_timezone_description_by_id((int) $entity["id_tz"]);
        $entity["date_from"] = $utc->get_dt_into_tz($entity["date_from"], $tzfrom);
        $entity["date_to"] = $utc->get_dt_into_tz($entity["date_to"], $tzfrom);
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
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}