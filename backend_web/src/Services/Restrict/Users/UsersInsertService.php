<?php
namespace App\Services\Restrict\Users;
use App\Factories\ComponentFactory as CF;
use App\Factories\RepositoryFactory;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Enums\Key;


final class UsersInsertService extends AppService
{
    use SessionTrait;
    private array $input;
    private UserRepository $repository;

    public function __construct(array $input)
    {
        $this->repository = RepositoryFactory::get("Base/UserRepository");
        $this->input = $input;
        $this->_sessioninit();
    }

    public function __invoke(): string
    {
        $insert = $this->input;
        $f = array_filter(array_keys($insert), function ($k){ return substr($k,0,1)!=="_"; });
        $insert = array_intersect_key($insert, array_flip($f));
        $insert["secret"] = $insert["password"];
        unset($insert["password"]);
        $insert["insert_user"] = $this->session->get(Key::AUTH_USER)["id"] ?? "";
        $insert["insert_date"] = date("Y-m-d H:i:s");

        if(!$insert) $this->_exeption(__("No data"));
        $r = $this->repository->insert($insert, false);

        return $r;
    }
}