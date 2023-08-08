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

    private AuthService $authService;
    private array $authUser;

    private UserRepository $userRepository;
    private UserPermissionsRepository $userPermissionsRepository;
    private FieldsValidator $fieldsValidator;
    private UserPermissionsEntity $userPermissionsEntity;
    private int $idUser;

    public function __construct(array $input)
    {
        $this->authService = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$userUuid = $this->input["_useruuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->userRepository = RF::get(UserRepository::class);
        if (!$this->idUser = $this->userRepository->get_id_by_uuid($userUuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $userUuid));

        if ($this->idUser === 1)
            $this->_exception(__("You can not add permissions to this user"));

        $this->userPermissionsEntity = MF::get(UserPermissionsEntity::class);
        $this->userPermissionsRepository = RF::get(UserPermissionsRepository::class)->set_model($this->userPermissionsEntity);
        $this->authUser = $this->authService->get_user();
    }

    public function __invoke(): array
    {
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_check_entity_permission();

        $update["_new"] = false;
        $this->fieldsValidator = VF::get($update, $this->userPermissionsEntity);

        return ($permissions = $this->userPermissionsRepository->get_by_user($this->idUser))
            ? $this->_update($update, $permissions)
            : $this->_insert($update);
    }

    private function _check_permission(): void
    {
        if($this->authService->is_root_super()) return;

        if(!$this->authService->is_user_allowed(UserPolicyType::USER_PERMISSIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->authService->is_root_super()) return;

        //si el us en sesion se quiere agregar permisos
        $permuser = $this->userRepository->get_by_id($this->idUser);
        $idauthuser = (int) $this->authUser["id"];
        if ($idauthuser === $this->idUser)
            $this->_exception(__("You are not allowed to change your own permissions"));

        //un root puede cambiar el de cualquiera (menos el de el mismo, if anterior)
        if ($this->authService->is_root()) return;

        //un sysadmin puede cambiar solo a los que tiene debajo
        if ($this->authService->is_sysadmin() && $this->authService->is_business($permuser["id_profile"])) return;

        $identowner = $this->userRepository->get_idowner($this->idUser);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->authService->is_business_owner()
            && $this->authService->is_business_manager($permuser["id_profile"])
            && ((int) $this->authUser["id"]) === $identowner
        )
            return;

        //to-do, solo se pueden agregar los permisos que tiene el owner ninguno más

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _skip_validation_insert(): self
    {
        $this->fieldsValidator
            ->add_skip("id")
            ->add_skip("uuid")
            ->add_skip("id_user")
        ;
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->fieldsValidator
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
            ->add_rule("json_rw", "json_rw", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
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
                //cuidado con esto. Un servicio no deberia deovlver html solo texto plano
                //para los casos en los que es consumido por otra interfaz
                $valid = "\"".implode("\",<br/>\"", $allpolicies)."\"";
                return __("Invalid policies: {0} <br/>Valid are:<br/>{1}", $invalid, $valid);
            })
        ;
        
        return $this->fieldsValidator;
    }

    private function _is_valid_json(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
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

        $update = $this->userPermissionsEntity->map_request($update);
        $this->_check_entity_permission();
        $this->userPermissionsEntity->add_sysupdate($update, $this->authUser["id"]);
        $this->userPermissionsRepository->update($update);
        return [
            "id" => $permissions["id"],
            "uuid" => $update["uuid"]
        ];
    }
    
    private function _insert(array $update): array
    {
        $update["_new"] = true;
        $this->fieldsValidator = VF::get($update, $this->userPermissionsEntity);
        if ($errors = $this->_skip_validation_insert()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $update["id_user"] = $this->idUser;
        $update["uuid"] = uniqid();
        $update = $this->userPermissionsEntity->map_request($update);
        $this->userPermissionsEntity->add_sysinsert($update, $this->authUser["id"]);
        $id = $this->userPermissionsRepository->insert($update);
        return [
            "id" => $id,
            "uuid" => $update["uuid"]
        ];
    }
}