<?php
namespace App\Services\Restrict\Users;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Services\Auth\AuthService;
use App\Repositories\Base\UserRepository;
use App\Repositories\Base\UserPermissionsRepository;
use App\Repositories\Base\UserPreferencesRepository;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;

final class UsersInfoService extends AppService
{
    private AuthService $auth;
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;
    private UserPreferencesRepository $repoprefs;
    private array $authuser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No user code provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->repouser = RF::get("Base/User");
        $this->repopermission = RF::get("Base/UserPermissions");
        $this->repoprefs = RF::get("Base/UserPreferences");
        $this->authuser = $this->auth->get_user();
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(PolicyType::USERS_READ)
            || $this->auth->is_user_allowed(PolicyType::USERS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function __invoke(): array
    {
        $user = $this->repouser->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found", $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($user);
        return [
            "user" => $user,
            "permissions" => $this->repopermission->get_by_user($iduser = $user["id"]),
            "preferences" => $this->repoprefs->get_by_user($iduser),
        ];
    }

    private function _check_entity_permission(array $entity): void
    {
        $iduser = $this->repouser->get_id_by($entity["uuid"]);
        if ($this->auth->is_root() || ((int)$this->authuser["id"]) === $iduser) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $idowner = $this->repouser->get_owner($iduser);
        $idowner = $idowner["id"];
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $this->authuser["id"] === $idowner
        )
            return;

        $this->_exception(__("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN);
    }

    public function get_for_edit(): array
    {
        $user = $this->repouser->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($user);
        if($birthdate = $user["birthdate"]) $user["birthdate"] = substr($birthdate, 0,10);
        return $user;
    }
}