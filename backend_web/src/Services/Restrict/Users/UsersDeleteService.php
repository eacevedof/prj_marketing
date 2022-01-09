<?php
namespace App\Services\Restrict\Users;

use App\Services\AppService;
use App\Services\Auth\AuthService;
use App\Factories\EntityFactory as MF;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Models\Base\UserEntity;
use App\Repositories\Base\UserRepository;
use App\Enums\PolicyType;
use App\Enums\ProfileType;
use App\Enums\ExceptionType;

final class UsersDeleteService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private UserRepository $repouser;
    private UserEntity $entityuser;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if(!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->entityuser = MF::get("Base/User");
        $this->repouser = RF::get("Base/UserRepository")->set_model($this->entityuser);
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::USERS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_delete_permission(array $entity): void
    {
        $iduser = $this->repouser->get_id_by($entity["uuid"]);
        $idauthuser = (int)$this->authuser["id"];

        //si el logado quiere borrarse a si mismo
        if ($idauthuser === $iduser)
            $this->_exception(
                __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
            );

        if ($this->auth->is_root()) return;

        if ($this->auth->is_sysadmin()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_OWNER, ProfileType::BUSINESS_MANAGER])
        )
            return;

        $identyowner = $this->repouser->get_ownerid($iduser);
        //si el usuario logado es owner y quiere eliminar un manager que le pertenece
        if ($this->auth->is_business_owner()
            && in_array($entity["id_profile"], [ProfileType::BUSINESS_MANAGER])
            && $idauthuser === $identyowner
        )
            return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _check_entity_undelete_permission(array $entity): void
    {
        $iduser = $this->repouser->get_id_by($entity["uuid"]);
        $idauthuser = (int) $this->authuser["id"];
        if ($idauthuser === $iduser)
            $this->_exception(
                __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
            );

        if ($this->auth->is_root()) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $entity = $this->input;
        if (!$iduser = $this->repouser->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $iduser;
        if (!$this->entityuser->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if ($entity["delete_date"])
            $this->_exception(
                __("Is not possible to delete entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $this->_check_entity_delete_permission($entity);

        $updatedate = $this->repouser->get_sysupdate($entity);
        $this->entityuser->add_sysdelete($entity, $updatedate, $this->authuser["id"]);
        $affected = $this->repouser->update($entity);
        //$this->repouser->delete($entity);
        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }
    
    public function undelete(): array
    {
        $entity = $this->input;
        if (!$id = $this->repouser->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $id;
        if (!$this->entityuser->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        $entity = $this->repouser->get_by_id($id);
        if (!$entity["delete_date"])
            $this->_exception(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $this->_check_entity_undelete_permission($entity);
        $iduser = $this->authuser["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $id,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repouser->get_csvcru($entity, $id),
        ];

        $this->entityuser->add_sysupdate($entity, $iduser);
        $affected = $this->repouser->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];                
    }
}