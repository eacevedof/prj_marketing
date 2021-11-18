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

    public function __invoke(): array
    {
        $insert = $this->input;
        if(!$insert) $this->_exeption(__("No data"));
        $r = $this->repository->insert($insert);

        return $r;
    }
}