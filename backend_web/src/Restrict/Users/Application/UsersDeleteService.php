<?php
namespace App\Restrict\Users\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Users\Domain\UserEntity;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Infrastructure\Enums\PolicyType;
use App\Shared\Infrastructure\Enums\ProfileType;
use App\Shared\Infrastructure\Enums\ExceptionType;

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
        $this->entityuser = MF::get(UserEntity::class);
        $this->repouser = RF::get(UserRepository::class)->set_model($this->entityuser);
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
        $iduser = (int)$entity["id"];
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

        $identyowner = $this->repouser->get_idowner($iduser);
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
        $iduser = (int) $entity["id"];
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

        if ($this->repouser->is_deleted($iduser))
            $this->_exception(
                __("This item is already deleted {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repouser->get_by_id($iduser);
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
        if (!$iduser = $this->repouser->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $iduser;
        if (!$this->entityuser->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if (!$this->repouser->is_deleted($iduser))
            $this->_exception(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repouser->get_by_id($iduser);
        $this->_check_entity_undelete_permission($entity);
        $idauthuser = $this->authuser["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $iduser,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repouser->get_csvcru($entity, $idauthuser),
        ];

        $this->entityuser->add_sysupdate($entity, $idauthuser);
        $affected = $this->repouser->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];                
    }
}