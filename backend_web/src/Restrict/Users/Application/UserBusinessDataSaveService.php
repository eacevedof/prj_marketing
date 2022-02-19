<?php
namespace App\Restrict\Users\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\UserPermissionsEntity;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;


final class UserBusinessDataSaveService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private BusinessDataRepository $repobusinessdata;
    private FieldsValidator $validator;
    private UserPermissionsEntity $entitybusinessdata;
    private int $iduser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->repouser = RF::get(UserRepository::class);
        if (!$this->iduser = $this->repouser->get_id_by_uuid($useruuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $useruuid));
        if ($this->iduser === 1)
            $this->_exception(__("You can not add permissions to this user"));

        $this->entitybusinessdata = MF::get(UserPermissionsEntity::class);
        $this->repobusinessdata = RF::get(BusinessDataRepository::class)->set_model($this->entitybusinessdata);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::USER_PERMISSIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->auth->is_root_super()) return;

        //si el us en sesion se quiere agregar permisos
        $permuser = $this->repouser->get_by_id($this->iduser);
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $this->iduser)
            $this->_exception(__("You are not allowed to change your own permissions"));

        //un root puede cambiar el de cualquiera (menos el de el mismo, if anterior)
        if ($this->auth->is_root()) return;

        //un sysadmin puede cambiar solo a los que tiene debajo
        if ($this->auth->is_sysadmin() && $this->auth->is_business($permuser["id_profile"])) return;

        $identowner = $this->repouser->get_idowner($this->iduser);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && $this->auth->is_business_manager($permuser["id_profile"])
            && ((int) $this->authuser["id"]) === $identowner
        )
            return;

        //to-do, solo se pueden agregar los permisos que tiene el owner ninguno más

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


            ->add_rule("user_logo_1", "user_logo_1", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("user_logo_2", "user_logo_2", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("user_logo_3", "user_logo_3", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })

            ->add_rule("url_favicon", "url_favicon", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })

            ->add_rule("head_bgcolor", "head_bgcolor", function ($data) {
                if (!$value = $data["value"]) return false;
                return !$this->is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("head_color", "head_color", function ($data) {
                if (!$value = $data["value"]) return false;
                return !$this->is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("head_bgimage", "head_bgimage", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("body_bgcolor", "body_bgcolor", function ($data) {
                if (!$value = $data["value"]) return false;
                return !$this->is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("body_color", "body_color", function ($data) {
                if (!$value = $data["value"]) return false;
                return !$this->is_valid_color($value) ? __("Invalid hex color"): false;
            })
            ->add_rule("body_bgimage", "body_bgimage", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("site", "site", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_fb", "url_social_fb", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_ig", "url_social_ig", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_twitter", "url_social_twitter", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
            ->add_rule("url_social_tiktok", "url_social_tiktok", function ($data) {
                if (!$value = $data["value"]) return false;
                return !filter_var($value, FILTER_VALIDATE_URL) ? __("Invalid url value") : false;
            })
        ;
        
        return $this->validator;
    }

    private function is_valid_color(string $hexcolor): bool
    {
        $hexcolor = ltrim($hexcolor, "#");
        if (
            ctype_xdigit($hexcolor) &&
            (strlen($hexcolor) == 6 || strlen($hexcolor) == 3))
            return true;
        return false;
    }
    
    private function _update(array $update, array $permissions): array
    {
        if ($permissions["id"] !== $update["id"])
            $this->_exception(
                __("This permission does not belong to user {0}", $this->input["_useruuid"]),
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
            "id" => $permissions["id"],
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
        $update["id_user"] = $this->iduser;
        $update["uuid"] = uniqid();
        $update = $this->entitybusinessdata->map_request($update);
        $this->entitybusinessdata->add_sysinsert($update, $this->authuser["id"]);
        $id = $this->repobusinessdata->insert($update);
        return [
            "id" => $id,
            "uuid" => $update["uuid"]
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

        return ($permissions = $this->repobusinessdata->get_all_by_user($this->iduser))
            ? $this->_update($update, $permissions)
            : $this->_insert($update);
    }
}