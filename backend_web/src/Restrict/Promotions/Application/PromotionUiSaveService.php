<?php
namespace App\Restrict\Promotions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Restrict\Promotions\Domain\PromotionEntity;
use App\Restrict\Promotions\Domain\PromotionUiEntity;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class PromotionUiSaveService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;
    private FieldsValidator $validator;
    private PromotionUiEntity $entitypromotionui;
    private int $iduser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"])
            $this->_exception(__("No {0} code provided", __("user")),ExceptionType::CODE_BAD_REQUEST);

        $this->repopromotion = RF::get(PromotionRepository::class);
        if (!$this->iduser = $this->repopromotion->get_id_by_uuid($useruuid))
            $this->_exception(__("{0} with code {1} not found", __("User"), $useruuid));
        if ($this->iduser === 1)
            $this->_exception(__("You can not add permissions to this user"));

        $this->entitypromotionui = MF::get(PromotionUiEntity::class);
        $this->repopromotionui = RF::get(PromotionUiRepository::class)->set_model($this->entitypromotionui);
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
        $permuser = $this->repopromotion->get_by_id($this->iduser);
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $this->iduser)
            $this->_exception(__("You are not allowed to change your own permissions"));

        //un root puede cambiar el de cualquiera (menos el de el mismo, if anterior)
        if ($this->auth->is_root()) return;

        //un sysadmin puede cambiar solo a los que tiene debajo
        if ($this->auth->is_sysadmin() && $this->auth->is_business($permuser["id_profile"])) return;

        $identowner = $this->repopromotion->get_idowner($this->iduser);
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
        
        return $this->validator;
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

        $update = $this->entitypromotionui->map_request($update);
        $this->_check_entity_permission();
        $this->entitypromotionui->add_sysupdate($update, $this->authuser["id"]);
        $this->repopromotionui->update($update);
        return [
            "id" => $permissions["id"],
            "uuid" => $update["uuid"]
        ];
    }
    
    private function _insert(array $update): array
    {
        $update["_new"] = true;
        $this->validator = VF::get($update, $this->entitypromotionui);
        if ($errors = $this->_skip_validation_insert()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $update["id_user"] = $this->iduser;
        $update["uuid"] = uniqid();
        $update = $this->entitypromotionui->map_request($update);
        $this->entitypromotionui->add_sysinsert($update, $this->authuser["id"]);
        $id = $this->repopromotionui->insert($update);
        return [
            "id" => $id,
            "uuid" => $update["uuid"]
        ];
    }

    public function __invoke(): array
    {
        if (!$update = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->_check_entity_permission();

        $update["_new"] = false;
        $this->validator = VF::get($update, $this->entitypromotionui);

        return ($permissions = $this->repopromotionui->get_by_user($this->iduser))
            ? $this->_update($update, $permissions)
            : $this->_insert($update);
    }
}