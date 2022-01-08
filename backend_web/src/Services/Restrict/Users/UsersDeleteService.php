<?php
namespace App\Services\Restrict\Users;

use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Services\AppService;
use App\Services\Auth\AuthService;
use App\Factories\EntityFactory as MF;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Models\Base\UserEntity;
use App\Repositories\Base\UserRepository;
use App\Enums\ExceptionType;


final class UsersDeleteService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private UserRepository $repouser;
    private UserEntity $modeluser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if(!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->modeluser = MF::get("Base/User");
        $this->repouser = RF::get("Base/UserRepository")->set_model($this->modeluser);
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::USERS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_permission(array $entity): void
    {
        $iduser = $this->repouser->get_id_by($entity["uuid"]);
        if ($this->auth->is_root() && ((int)$this->authuser["id"]) !== $iduser) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $idowner = $this->repouser->get_owner($iduser);
        $idowner = $idowner["id"];
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $this->authuser["id"] === $idowner
        )
            return;

        $this->_exception(__("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN);
    }

    public function __invoke(): array
    {
        $update = $this->input;
        if (!$id = $this->repouser->get_id_by($update["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->modeluser->do_match_keys($update))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $this->_check_entity_permission($update);
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
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $update["id"] = $id;
        if (!$this->modeluser->do_match_keys($update))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $row = $this->repouser->get_by_id($id);
        $iduser = $this->authuser["id"];

        $update = [
            "uuid" => $update["uuid"],
            "id" => $id,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repouser->get_csvcru($row, $id),
        ];

        $this->modeluser->add_sysupdate($update, $iduser);
        $affected = $this->repouser->update($update);

        return [
            "affected" => $affected,
            "uuid" => $update["uuid"]
        ];                
    }
}