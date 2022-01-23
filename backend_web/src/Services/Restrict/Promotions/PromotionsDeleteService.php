<?php
namespace App\Services\Restrict\Promotions;

use App\Services\AppService;
use App\Factories\EntityFactory as MF;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Models\App\PromotionEntity;
use App\Repositories\App\PromotionRepository;
use App\Services\Auth\AuthService;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;

final class PromotionsDeleteService extends AppService
{
    private AuthService $auth;
    private array $authuser;
    private PromotionRepository $repopromotion;
    private PromotionEntity $entitypromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        if(!$this->input["uuid"])
            $this->_exception(__("Empty required code"),ExceptionType::CODE_BAD_REQUEST);

        $this->authuser = $this->auth->get_user();
        $this->entitypromotion = MF::get("App/Promotion");
        $this->repopromotion = RF::get("App/PromotionRepository")->set_model($this->entitypromotion);
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    private function _check_entity_delete_permission(array $entity): void
    {
        $idpromotion = (int)$entity["id"];
        $idauthuser = (int)$this->authuser["id_owner"];

        if ($this->auth->is_root() || $this->auth->is_sysadmin()) return;

        if ($this->auth->is_business_owner() || $this->auth->is_business_manager()) {
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
        if (!$idpromotion = $this->repopromotion->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $idpromotion;
        if (!$this->entitypromotion->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if ($this->repopromotion->is_deleted($idpromotion))
            $this->_exception(
                __("This item is already deleted {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repopromotion->get_by_id($idpromotion);
        $this->_check_entity_delete_permission($entity);

        $updatedate = $this->repopromotion->get_sysupdate($entity);
        $this->entitypromotion->add_sysdelete($entity, $updatedate, $this->authuser["id"]);
        $affected = $this->repopromotion->update($entity);
        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }
    
    public function undelete(): array
    {
        $entity = $this->input;
        if (!$idpromotion = $this->repopromotion->get_id_by($entity["uuid"]))
            $this->_exception(__("Data not found"),ExceptionType::CODE_NOT_FOUND);

        $entity["id"] = $idpromotion;
        if (!$this->entitypromotion->do_match_keys($entity))
            $this->_exception(__("Not all keys provided"),ExceptionType::CODE_BAD_REQUEST);

        if (!$this->repopromotion->is_deleted($idpromotion))
            $this->_exception(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );

        $entity = $this->repopromotion->get_by_id($idpromotion);
        //todo revisar si es necesario
        $this->_check_entity_undelete_permission($entity);
        $idauthuser = $this->authuser["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $idpromotion,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repopromotion->get_csvcru($entity, $idauthuser),
        ];

        $this->entitypromotion->add_sysupdate($entity, $idauthuser);
        $affected = $this->repopromotion->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];                
    }
}