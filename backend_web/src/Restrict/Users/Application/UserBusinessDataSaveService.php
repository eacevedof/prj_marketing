<?php
namespace App\Restrict\Users\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Checker\Application\CheckerService;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\BusinessData\Domain\BusinessDataEntity;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class UserBusinessDataSaveService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private BusinessDataRepository $repobusinessdata;
    private FieldsValidator $validator;
    private BusinessDataEntity $entitybusinessdata;
    private int $bdiduser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->repouser = RF::get(UserRepository::class);
        if (!$this->bdiduser = $this->repouser->get_id_by_uuid($useruuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $useruuid));

        $this->entitybusinessdata = MF::get(BusinessDataEntity::class);
        $this->repobusinessdata = RF::get(BusinessDataRepository::class)->set_model($this->entitybusinessdata);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->auth->is_system()) return;

        //si el us en sesion quiere cambiar su bd
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $this->bdiduser)
            return;

        //si el propietario del us de sesion coincide con el de la entidad
        if ($this->auth->get_idowner() === $this->bdiduser)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _skip_validation_insert(): self
    {
        $this->validator
            ->add_skip("id")
            ->add_skip("uuid")
            ->add_skip("id_user")
            ->add_skip("slug")
        ;
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id", "id", function ($data) {
                if ($data["data"]["_new"]) return false;
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("uuid", "uuid", function ($data) {
                if ($data["data"]["_new"]) return false;
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_user", "id_user", function ($data) {
                if ($data["data"]["_new"]) return false;
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_tz", "id_tz", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("business_name", "business_name", function ($data) {
                if (!$data["data"]["_new"]) return false;
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("user_logo_1", "user_logo_1", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("user_logo_2", "user_logo_2", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("user_logo_3", "user_logo_3", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })

            ->add_rule("url_favicon", "url_favicon", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })

            ->add_rule("head_bgcolor", "head_bgcolor", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("head_color", "head_color", function ($data) {
                if (!$value = $data["value"]) return false;

                return !CheckerService::is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("head_bgimage", "head_bgimage", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("body_bgcolor", "body_bgcolor", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("body_color", "body_color", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("body_bgimage", "body_bgimage", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("url_business", "url_business", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_fb", "url_social_fb", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_ig", "url_social_ig", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_twitter", "url_social_twitter", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_tiktok", "url_social_tiktok", function ($data) {
                if (!$value = $data["value"]) return false;
                return !CheckerService::is_valid_url($value) ? __("Invalid url value") : false;
            })
        ;

        return $this->validator;
    }

    private function _update(array $update, array $businessdata): array
    {
        unset($update["business_name"]);
        if ($businessdata["id"] !== $update["id"])
            $this->_exception(
                __("This {0} does not belong to user {1}", __("Business data") ,$this->input["_useruuid"]),
                ExceptionType::CODE_BAD_REQUEST
            );

        //no se hace skip pq se tiene que cumplir todo
        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitybusinessdata->map_request($update);
        $this->_check_entity_permission();
        $this->entitybusinessdata->add_sysupdate($update, $this->authuser["id"]);
        $this->repobusinessdata->update($update);
        return [
            "id" => $businessdata["id"],
            "uuid" => $update["uuid"]
        ];
    }

    private function _insert(array $update): array
    {
        $update["_new"] = true;
        $this->validator = VF::get($update, $this->entitybusinessdata);
        if ($errors = $this->_skip_validation_insert()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $update["id_user"] = $this->bdiduser;
        $update["uuid"] = uniqid();
        $update["slug"] = CF::get(TextComponent::class)->slug($update["business_name"])."-$this->bdiduser";
        $update = $this->entitybusinessdata->map_request($update);
        $this->entitybusinessdata->add_sysinsert($update, $this->authuser["id"]);
        $id = $this->repobusinessdata->insert($update);
        return [
            "id" => $id,
            "uuid" => $update["uuid"],
            "slug" => $update["slug"]
        ];
    }

    public function __invoke(): array
    {
        unset($this->input["slug"]);
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_check_entity_permission();

        $update["_new"] = false;
        $this->validator = VF::get($update, $this->entitybusinessdata);

        return ($businessdata = $this->repobusinessdata->get_by_user($this->bdiduser))
            ? $this->_update($update, $businessdata)
            : $this->_insert($update);
    }
}