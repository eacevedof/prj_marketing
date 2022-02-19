<?php
namespace App\Restrict\Users\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Users\Domain\UserPermissionsRepository;
use App\Restrict\Users\Domain\UserPreferencesRepository;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ExceptionType;

final class UsersInfoService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;
    private UserPreferencesRepository $repoprefs;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        if(!$this->input = $input[0] ?? "")
            $this->_exception(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->repouser = RF::get(UserRepository::class);
        $this->repopermission = RF::get(UserPermissionsRepository::class);
        $this->repoprefs = RF::get(UserPreferencesRepository::class);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::USERS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::USERS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $iduser = (int) $entity["id"];
        $idauthuser = (int)$this->authuser["id"];
        if ($this->auth->is_root() || $idauthuser === $iduser) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [UserProfileType::SYS_ADMIN, UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        )
            return;

        $identowner = $this->repouser->get_idowner($iduser);
        //si logado es propietario del bm
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_MANAGER])
            && $idauthuser === $identowner
        )
            return;

        //si el logado es bm y la ent es del mismo owner
        $idauthowner = $this->repouser->get_idowner($idauthuser);
        if ($this->auth->is_business_manager() && $idauthowner === $identowner)
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $user = $this->repouser->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("{0} with code {1} not found", __("User"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_entity_permission($user);
        return [
            "user" => $user,
            "permissions" => $this->repopermission->get_by_user($iduser = $user["id"]),
            "preferences" => $this->repoprefs->get_by_user($iduser),
            "businessdata" => "to-do",
        ];
    }

    public function get_for_edit(): array
    {
        $user = $this->repouser->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found", $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_entity_permission($user);
        if($birthdate = $user["birthdate"]) $user["birthdate"] = substr($birthdate, 0,10);
        return $user;
    }
}