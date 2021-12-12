<?php
namespace App\Services\Restrict\Users;
use App\Factories\RepositoryFactory;
use App\Models\Base\UserModel;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Enums\KeyType;
use App\Factories\ModelFactory;
use App\Traits\RequestTrait;
use App\Enums\ExceptionType;


final class UsersDeleteService extends AppService
{
    use SessionTrait;
    use RequestTrait;

    private array $user;
    private UserRepository $repository;
    private UserModel $model;

    public function __construct(array $input)
    {
        $this->model = ModelFactory::get("Base/User");
        $this->repository = RepositoryFactory::get("Base/UserRepository")->set_model($this->model);
        $this->_load_request($input);
        $this->user = $this->_sessioninit()->get(KeyType::AUTH_USER);
    }

    public function __invoke(): array
    {
        $update = $this->_get_without_operations();
        if (!$update)
            $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if (!$id = $this->repository->get_id_by($update["uuid"] ?? ""))
            $this->_exeption(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->model->do_match_keys($update))
            $this->_exeption(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $updatedate = $this->repository->get_sysupdate($update);
        $this->model->add_sysdelete($update, $updatedate, $this->user["id"]);
        $affected = $this->repository->update($update);
        //$this->repository->delete($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
    
    public function undelete(): array
    {
        $update = $this->_get_without_operations();
        if (!$update)
            $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        if (!$id = $this->repository->get_id_by($update["uuid"] ?? ""))
            $this->_exeption(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->model->do_match_keys($update))
            $this->_exeption(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        //obtener fecha de borrado, csv
        $updatedate = $this->repository->get_sysupdate($update);
        $this->model->add_sysdelete($update, $updatedate, $this->user["id"]);
        $affected = $this->repository->update($update);
        //$this->repository->delete($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];                
    }
}