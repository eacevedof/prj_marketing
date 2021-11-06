<?php
namespace App\Services\Restrict\Users;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;
use App\Enums\Key;
use \Exception;

final class UsersSearchService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $domain;
    private array $input;
    private ComponentEncdecrypt $encdec;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_sessioninit();
        $this->_cookieinit()
            ->set_name("nombre")
            ->set_domain("localhost")
            ->set_valid_path("/")
        ;

        $this->encdec = $this->_get_encdec();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
    }

    public function __invoke(): array
    {
        $rows = $this->repository->search($this->input);
        $i = count($rows);
        return [
            "recordsFiltered" => $i,
            "recordsTotal" => $i,
            "data"=> $rows
        ];
    }
}