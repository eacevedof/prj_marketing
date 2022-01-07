<?php
namespace App\Services\Restrict\Users;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Repositories\Base\UserPermissionsRepository;
use App\Repositories\Base\UserRepository;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;

final class UsersInfoService extends AppService
{
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;
    private array $authuser;

    public function __construct(array $input)
    {
        $this->input = $input[0] ?? "";
        if(!$this->input)
            $this->_exception(__("No user code provided"), ExceptionType::CODE_BAD_REQUEST);

        $this->repouser = RF::get("Base/User");
        $this->repopermission = RF::get("Base/UserPermissions");
        $this->authuser = SF::get_auth()->get_user();
    }

    public function __invoke(): array
    {
        $user = $this->repouser->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $this->_check_permission($user);
        $permissions = $this->repopermission->get_by_user($user["id"]);
        return [
            "user" => $user,
            "permissions" => $permissions
        ];
    }

    private function _check_permission(array $entity): void
    {
        $auth = SF::get_auth();
        $iduser = $this->repouser->get_id_by($entity["uuid"]);
        if ($auth->is_root() || (((int)$this->authuser["id"]) === $iduser)) return;

        if ($auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $idowner = $this->repouser->get_owner($iduser);
        $idowner = $idowner["id"];
        if ($auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $this->authuser["id"] === $idowner
        )
            return;

        $this->_exception(__("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN);
    }

    public function get_edit(): array
    {
        $user = $this->repouser->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        $this->_check_permission($user);
        if($birthdate = $user["birthdate"]) $user["birthdate"] = substr($birthdate, 0,10);
        return $user;
    }
}