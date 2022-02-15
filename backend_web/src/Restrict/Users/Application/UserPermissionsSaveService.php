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
use App\Restrict\Users\Domain\UserPermissionsRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class UserPermissionsSaveService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private UserPermissionsRepository $repouserpermissions;
    private FieldsValidator $validator;
    private UserPermissionsEntity $entityuserpermissions;
    private int $iduser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"])
            $this->_exception(__("Empty user code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entityuserpermissions = MF::get(UserPermissionsEntity::class);
        $this->repouser = RF::get(UserRepository::class);
        $this->repouserpermissions = RF::get(UserPermissionsRepository::class);
        $this->repouserpermissions->set_model($this->entityuserpermissions);
        $this->authuser = $this->auth->get_user();

        if (!$this->iduser = $this->repouser->get_id_by($useruuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $useruuid));
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::USER_PERMISSIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        return;

        //tengo que recuperar el usuario del permiso y ver en que nivel está y si pertenece al bow para que le pueda
        //dar permisos
        $iduserpermission = $this->repouserpermissions->get_id_by($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $iduserpermission) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repouserpermissions->get_idowner($iduserpermission);
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

    private function _skip_validation_create(): self
    {
        $this->validator
            ->add_skip("id")
            ->add_skip("uuid")
            ->add_skip("id_user")
        ;
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->add_rule("id", "id", function ($data) {
                if ($data["data"]["_new"]) return false;
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("uuid", "uuid", function ($data) {
                if ($data["data"]["_new"]) return false;
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_user", "id_user", function ($data) {
                if ($data["data"]["_new"]) return false;
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("json_rw", "json_rw", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("json_rw", "valid_json", function ($data){
                return $this->_is_valid_json($data["value"]) ? false : __("Invalid Json document");
            })
            ->add_rule("json_rw", "valid rules", function ($data){
                $values = json_decode($data["value"], 1);
                if (!$values) return false;

                $allpolicies = UserPolicyType::get_all();
                $invalid = [];
                foreach ($values as $policy){
                    if (!in_array($policy, $allpolicies))
                        $invalid[] = $policy;
                }
                if (!$invalid) return false;
                $invalid = implode(", ",$invalid);
                $valid = implode(", ", $allpolicies);
                return __("Invalid policies: {0} <br/>Valid are: {1}", $invalid, $valid);
            })
        ;
        return $this->validator;
    }

    private function _is_valid_json(string $string): bool
    {
        $r = json_decode($string, 1);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function __invoke(): array
    {
        //hace trim
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->validator = VF::get($this->input, $this->entityuserpermissions);
        if(!$permissions = $this->repouserpermissions->get_all_by_user($this->iduser)) {
            $this->input["_new"] = true;
            $this->validator = VF::get($this->input, $this->entityuserpermissions);

            if ($errors = $this->_skip_validation_create()->_add_rules()->get_errors()) {
                $this->_set_errors($errors);
                throw new FieldsException(__("Fields validation errors"));
            }
            $update["id_user"] = $this->iduser;
            $update["uuid"] = uniqid();
            $update = $this->entityuserpermissions->map_request($update);
            $this->entityuserpermissions->add_sysinsert($update, $this->authuser["id"]);
            $affected = $this->repouserpermissions->insert($update);
            return [
                "affected" => $affected,
                "uuid" => $update["uuid"]
            ];
        }

        if ($permissions["id"] !== $update["id"])
            $this->_exception(
                __("This permission does not belong to user {0}", $this->input["_useruuid"]),
                ExceptionType::CODE_BAD_REQUEST
            );


        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entityuserpermissions->map_request($update);
        $this->_check_entity_permission($update);
        $this->entityuserpermissions->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repouserpermissions->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}