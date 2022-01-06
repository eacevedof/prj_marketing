<?php
namespace App\Services\Restrict\Users;
use App\Services\AppService;
use App\Factories\ModelFactory as MF;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Models\Base\UserModel;
use App\Repositories\Base\UserRepository;
use App\Enums\ExceptionType;

final class UsersDeleteService extends AppService
{
    private array $authuser;
    private UserRepository $repouser;
    private UserModel $modeluser;

    public function __construct(array $input)
    {
        $this->input = $input;
        if(!$this->input["uuid"])
            $this->_exeption(__("Empty required data"),ExceptionType::CODE_BAD_REQUEST);
        $this->authuser = SF::get_auth()->get_user();
        if(!$this->authuser) $this->_exeption(__("No authenticated user"), ExceptionType::CODE_FORBIDDEN);
        $this->modeluser = MF::get("Base/User");
        $this->repouser = RF::get("Base/UserRepository")->set_model($this->modeluser);
    }

    public function __invoke(): array
    {
        $update = $this->input;
        if (!$id = $this->repouser->get_id_by($update["uuid"]))
            $this->_exeption(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->modeluser->do_match_keys($update))
            $this->_exeption(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $updatedate = $this->repouser->get_sysupdate($update);
        $this->modeluser->add_sysdelete($update, $updatedate, $this->authuser["id"]);
        $affected = $this->repouser->update($update);
        //$this->repouser->delete($update);
        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];
    }
    
    public function undelete(): array
    {
        $update = $this->input;
        if (!$id = $this->repouser->get_id_by($update["uuid"]))
            $this->_exeption(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->modeluser->do_match_keys($update))
            $this->_exeption(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $row = $this->repouser->get_by_id($id);
        $iduser = $this->authuser["id"];
        $now = date("Y-m-d H:i:s");
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

        $this->modeluser->add_sysupdate($update, $iduser);
        $affected = $this->repouser->update($update);

        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];                
    }
}