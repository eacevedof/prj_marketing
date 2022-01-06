<?php
namespace App\Services\Restrict\Users;
use App\Enums\ExceptionType;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Factories\RepositoryFactory as RF;

final class UsersInfoService extends AppService
{
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;

    public function __construct(array $input)
    {
        $this->input = $input[0] ?? "";
        if(!$this->input) {
            $this->_exception(__("No user code provided"), ExceptionType::CODE_BAD_REQUEST);
        }
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function __invoke(): array
    {
        $user = $this->repository->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );

        $permissions = $this->permissionrepo->get_by_user($user["id"]);
        return [
            "user" => $user,
            "permissions" => $permissions
        ];
    }

    public function get_edit(): array
    {
        $user = $this->repository->get_info($this->input);
        if(!$user)
            $this->_exception(
                __("User with code {0} not found",$this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        if($birthdate = $user["birthdate"]) $user["birthdate"] = substr($birthdate, 0,10);
        return $user;
    }
}