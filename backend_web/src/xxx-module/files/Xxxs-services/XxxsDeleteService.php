<?php
namespace App\Restrict\Xxxs\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\EntityFactory as MF;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Restrict\Xxxs\Domain\XxxEntity;
use App\Restrict\Xxxs\Domain\XxxRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class XxxsDeleteService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private XxxRepository $repoxxx;
    private XxxEntity $entityxxx;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if(!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->entityxxx = MF::get(XxxEntity::class);
        $this->repoxxx = RF::get(XxxRepository::class)->set_model($this->entityxxx);
    }

    private function _check_permission(): void
    {
        if($this->auth->is_root_super()) return;

        if(!$this->auth->is_user_allowed(UserPolicyType::XXXS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_delete_permission(array $entity): void
    {
        if ($this->auth->is_root() || $this->auth->is_sysadmin()) return;

        if ($this->auth->is_business_owner() || $this->auth->is_business_manager()) {
            if ((int)$entity["id_owner"] === $this->auth->get_idowner())
                //comprobar si es propietario de la entidad o su owner es el mismo que el de la propiedad
                return;
        }

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _check_entity_undelete_permission(array $entity): void
    {
        if ($this->auth->is_root()) return;

        $this->_exception(
            __("You are not allowed to perform this operation"), ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $entity = $this->input;
        if (!$idxxx = $this->repoxxx->get_id_by_uuid($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $idxxx;
        if (!$this->entityxxx->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if ($this->repoxxx->is_deleted($idxxx))
            $this->_exception(
                __("This item is already deleted {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repoxxx->get_by_id($idxxx);
        $this->_check_entity_delete_permission($entity);

        $updatedate = $this->repoxxx->get_sysupdate($entity);
        $this->entityxxx->add_sysdelete($entity, $updatedate, $this->authuser["id"]);
        $affected = $this->repoxxx->update($entity);
        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }

    public function undelete(): array
    {
        $entity = $this->input;
        if (!$idxxx = $this->repoxxx->get_id_by_uuid($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $idxxx;
        if (!$this->entityxxx->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if (!$this->repoxxx->is_deleted($idxxx))
            $this->_exception(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repoxxx->get_by_id($idxxx);
        //todo revisar si es necesario
        $this->_check_entity_undelete_permission($entity);
        $idauthuser = $this->authuser["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $idxxx,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repoxxx->get_csvcru($entity, $idauthuser),
        ];

        $this->entityxxx->add_sysupdate($entity, $idauthuser);
        $affected = $this->repoxxx->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }
}