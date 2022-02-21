<?php
namespace App\Restrict\Users\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserEntity;
use App\Restrict\Users\Domain\UserRepository;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class UsersUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repouser;
    private FieldsValidator $validator;
    private UserEntity $entityuser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if (!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->entityuser = MF::get(UserEntity::class);
        $this->validator = VF::get($this->input, $this->entityuser);
        $this->repouser = RF::get(UserRepository::class);
        $this->repouser->set_model($this->entityuser);
        $this->authuser = $this->auth->get_user();
        $this->encdec = $this->_get_encdec();
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::USERS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $iduser = $this->repouser->get_id_by_uuid($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $iduser) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repouser->get_idowner($iduser);
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

    private function _skip_validation(): self
    {
        $this->validator->add_skip("password2");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $repouser = $this->repouser;
        $this->validator
            ->add_rule("email", "email", function ($data) use ($repouser){
                $email = trim($data["value"]);
                $uuid = $data["data"]["uuid"] ?? "";
                $id = $repouser->get_id_by_uuid($uuid);
                if (!$id) return __("{0} with code {1} not found", __("User"), $uuid);
                $idemail = $repouser->email_exists($email);
                if (!$idemail || ($id == $idemail)) return false;
                return __("This email already exists");
            })
            ->add_rule("email", "email", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("email", "email", function ($data) {
                return filter_var($data["value"], FILTER_VALIDATE_EMAIL) ? false : __("Invalid email format");
            })
            ->add_rule("phone", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("fullname", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("birthdate", "empty", function ($data) {
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_profile", "empty", function ($data){
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_parent", "by-profile", function ($data){
                if (($data["data"]["id_profile"] ?? "") === "4" && !trim($data["value"]))
                    return __("Empty field is not allowed");
                return false;
            })
            ->add_rule("id_country", "empty", function ($data){
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("id_language", "empty", function ($data){
                return trim($data["value"]) ? false : __("Empty field is not allowed");
            })
            ->add_rule("password", "not-equal", function ($data){
                if(!($password = trim($data["value"]))) return false;
                $password2 = trim($data["data"]["password2"] ?? "");
                return ($password === $password2) ? false : __("Bad password confirmation");
            })
        ;
        return $this->validator;
    }

    public function __invoke(): array
    {
        $update = $this->_get_req_without_ops($this->input);
        if (!$iduser = $this->repouser->get_id_by_uuid($uuid = $update["uuid"]))
            $this->_exception(__("{0} with code {1} not found", __("User"), $uuid), 404);

        if ($errors = $this->_skip_validation()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entityuser->map_request($update);
        $update["id"] = $iduser;
        $this->_check_entity_permission($update);
        if(!$update["secret"]) unset($update["secret"]);
        else
            $update["secret"] = $this->encdec->get_hashpassword($update["secret"]);
        $update["description"] = $update["fullname"];
        $this->entityuser->add_sysupdate($update, $this->authuser["id"]);

        $affected = $this->repouser->update($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
}