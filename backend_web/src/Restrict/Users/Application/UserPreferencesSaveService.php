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
use App\Restrict\Users\Domain\UserPreferencesEntity;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class UserPreferencesSaveService extends AppService
{
    use RequestTrait;

    private AuthService $auth;
    private array $authuser;

    private UserRepository $repouser;
    private UserPreferencesRepository $repouserprefs;
    private FieldsValidator $validator;
    private UserPreferencesEntity $entityuserprefs;
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

        $this->entityuserprefs = MF::get(UserPreferencesEntity::class);
        $this->repouserprefs = RF::get(UserPreferencesRepository::class)->set_model($this->entityuserprefs);
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::USER_PREFERENCES_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(int $id): void
    {
        if ($id) {
            if(!$id = $this->repouserprefs->get_by_id_and_user($id, $this->iduser))
                $this->_exception(
                    __("{0} {1} does not exist", __("Preference"), $id),
                    ExceptionType::CODE_BAD_REQUEST
                );
        }

        if ($this->auth->is_root_super() || $this->auth->is_root()) return;

        $prefuser = $this->repouser->get_by_id($this->iduser);
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $this->iduser) return;

        if ($this->auth->is_sysadmin() && $this->auth->is_business($prefuser["id_profile"])) return;

        $identowner = $this->repouser->get_idowner($this->iduser);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->auth->is_business_owner()
            && $this->auth->is_business_manager($prefuser["id_profile"])
            && ((int) $this->authuser["id"]) === $identowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _skip_validation_insert(): self
    {
        $this->validator
            ->add_skip("id")
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
            ->add_rule("pref_key", "pref_key", function ($data) {
                if ($data["data"]["_new"]) return false;
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->add_rule("pref_value", "pref_value", function ($data) {
                if ($data["data"]["_new"]) return false;
                return $data["value"] ? false : __("Empty field is not allowed");
            });
        return $this->validator;
    }

    private function _insert(array $prefreq): array
    {
        $this->validator = VF::get($prefreq, $this->entityuserprefs);
        if ($errors = $this->_skip_validation_insert()->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $prefreq["id_user"] = $this->iduser;
        $prefreq = $this->entityuserprefs->map_request($prefreq);
        $this->entityuserprefs->add_sysinsert($prefreq, $this->authuser["id"]);
        $this->repouserprefs->insert($prefreq);
        $result = $this->repouserprefs->get_by_user($this->iduser);

        return array_map(function ($row) {
            return [
                "id" => (int) $row["id"],
                "pref_key" => $row["pref_key"],
                "pref_value" => $row["pref_value"],
            ];
        }, $result);
    }

    private function _update(array $prefreq): array
    {
        $this->validator = VF::get($prefreq, $this->entityuserprefs);
        if ($errors = $this->_add_rules()->get_errors()) {
            $this->_set_errors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $prefreq = $this->entityuserprefs->map_request($prefreq);
        $this->_check_entity_permission((int) $prefreq["id"]);
        $this->entityuserprefs->add_sysupdate($prefreq, $this->authuser["id"]);
        $this->repouserprefs->update($prefreq);
        $result = $this->repouserprefs->get_by_user($this->iduser);

        return array_map(function ($row) {
            return [
                "id" => (int) $row["id"],
                "pref_key" => $row["pref_key"],
                "pref_value" => $row["pref_value"],
            ];
        }, $result);
    }

    public function __invoke(): array
    {
        if (!$prefreq = $this->_get_req_without_ops($this->input))
            $this->_exception(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $prefreq["_new"] = true;
        if($id = (int)($prefreq["id"] ?? "")) $prefreq["_new"] = false;

        return $id
            ? $this->_update($prefreq)
            : $this->_insert($prefreq);
    }
}