<?php
namespace App\Services\Restrict\Users;
use App\Enums\ExceptionType;
use App\Factories\ComponentFactory as CF;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;

final class UsersInfoService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $input;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;

    public function __construct(array $input)
    {
        $this->input = $input[0] ?? "";
        if(!$this->input) {
            $this->_exeption(__("No user code provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->_sessioninit();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function __invoke(): array
    {
        $user = $this->repository->get_info($this->input);
        if(!$user) return [];

        $permissions = $this->permissionrepo->get_by_user($user["id"]);
        return [
            "user" => $user,
            "permissions" => $permissions
        ];
    }
}