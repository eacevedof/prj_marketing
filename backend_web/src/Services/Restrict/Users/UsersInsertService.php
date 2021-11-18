<?php
namespace App\Services\Restrict\Users;
use App\Factories\ComponentFactory as CF;
use App\Factories\RepositoryFactory;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;


final class UsersInsertService extends AppService
{
    private array $input;
    private UserRepository $repository;

    public function __construct(array $input)
    {
        $this->repository = RepositoryFactory::get("Base/UserRepository");
        $this->input = $input;
    }

    public function __invoke(): string
    {
        $insert = $this->input;
        $f = array_filter(array_keys($insert), function ($k){ return substr($k,0,1)!=="_"; });
        $insert = array_intersect_key($insert, array_flip($f));
        $insert["secret"] = $insert["password"];
        unset($insert["password"]);

        if(!$insert) $this->_exeption(__("No data"));
        $r = $this->repository->insert($insert, false);

        return $r;
    }
}