<?php
namespace App\Services\Restrict\Users;
use App\Factories\ComponentFactory as CF;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;

final class UsersSearchService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $domain;
    private array $input;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_sessioninit();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        $rows = $this->repository->search($search);
        return $rows;
    }
}