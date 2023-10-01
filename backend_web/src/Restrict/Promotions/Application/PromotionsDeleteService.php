<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Promotions\Domain\{PromotionEntity, PromotionRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionsDeleteService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;
    private PromotionRepository $repopromotion;
    private PromotionEntity $entitypromotion;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        if (!$this->input["uuid"]) {
            $this->_throwException(__("Empty required code"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->entitypromotion = MF::getInstanceOf(PromotionEntity::class);
        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class)->setAppEntity($this->entitypromotion);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _check_entity_delete_permission(array $entity): void
    {
        if ($this->authService->isAuthUserRoot() || $this->authService->isAuthUserSysadmin()) {
            return;
        }

        if ($this->authService->isAuthUserBusinessOwner() || $this->authService->hasAuthUserBusinessManagerProfile()) {
            if ((int) $entity["id_owner"] === $this->authService->getIdOwner()) {
                //comprobar si es propietario de la entidad o su owner es el mismo que el de la propiedad
                return;
            }
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _check_entity_undelete_permission(array $entity): void
    {
        if ($this->authService->isAuthUserRoot()) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $entity = $this->input;
        if (!$idpromotion = $this->repopromotion->getEntityIdByEntityUuid($entity["uuid"])) {
            $this->_throwException(__("Data not found"), ExceptionType::CODE_NOT_FOUND);
        }

        $entity["id"] = $idpromotion;
        if (!$this->entitypromotion->areAllPksPresent($entity)) {
            $this->_throwException(__("Not all keys provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($this->repopromotion->isDeletedByEntityId($idpromotion)) {
            $this->_throwException(
                __("This item is already deleted {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );
        }

        $entity = $this->repopromotion->getEntityByEntityId($idpromotion);
        $this->_check_entity_delete_permission($entity);

        $updatedate = $this->repopromotion->getSysUpdateDateByPkFields($entity);
        $this->entitypromotion->addSysDelete($entity, $updatedate, $this->authUserArray["id"]);
        $affected = $this->repopromotion->update($entity);
        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }

    public function undelete(): array
    {
        $entity = $this->input;
        if (!$idpromotion = $this->repopromotion->getEntityIdByEntityUuid($entity["uuid"])) {
            $this->_throwException(__("Data not found"), ExceptionType::CODE_NOT_FOUND);
        }

        $entity["id"] = $idpromotion;
        if (!$this->entitypromotion->areAllPksPresent($entity)) {
            $this->_throwException(__("Not all keys provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$this->repopromotion->isDeletedByEntityId($idpromotion)) {
            $this->_throwException(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );
        }

        $entity = $this->repopromotion->getEntityByEntityId($idpromotion);
        //todo revisar si es necesario
        $this->_check_entity_undelete_permission($entity);
        $idauthuser = $this->authUserArray["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $idpromotion,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repopromotion->getCsvCru($entity, $idauthuser),
        ];

        $this->entitypromotion->addSysUpdate($entity, $idauthuser);
        $affected = $this->repopromotion->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }
}
