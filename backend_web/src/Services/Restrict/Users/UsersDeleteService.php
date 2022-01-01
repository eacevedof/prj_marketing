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
        $this->input = $input;
        if ($this->input["uuid"]) $this->_exeption(__("Empty data"),ExceptionType::CODE_BAD_REQUEST);

        $this->model = ModelFactory::get("Base/User");
        $this->repository = RepositoryFactory::get("Base/UserRepository")->set_model($this->model);
        $this->user = $this->_get_auth()->get_user();
    }

    public function __invoke(): array
    {
        $update = $this->input;
        if (!$id = $this->repository->get_id_by($update["uuid"]))
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
        $update = $this->input;
        if (!$id = $this->repository->get_id_by($update["uuid"]))
            $this->_exeption(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->model->do_match_keys($update))
            $this->_exeption(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $row = $this->repository->get_by_id($id);
        $iduser = $this->user["id"]; $now = date("Y-m-d H:i:s");
        $crucsv = $row["cru_csvnote"] ?? "";
        $crucsv = "delete_user:{$row["delete_user"]},delete_date:{$row["delete_date"]},delete_platform:{$row["delete_platform"]},($iduser:$now)|".$crucsv;
        $crucsv = substr($crucsv,0,499);

        $update = [
            "uuid" => $update["uuid"],
            "id" => $id,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $crucsv,
        ];
        $this->model->add_sysupdate($update, $iduser);
        $affected = $this->repository->update($update);

        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];                
    }
}