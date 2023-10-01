<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\{UserEntity, UserRepository};
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UsersDeleteService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;
    private UserRepository $repouser;
    private UserEntity $entityuser;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        if (!$this->input["uuid"]) {
            $this->_throwException(__("Empty required code"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->entityuser = MF::getInstanceOf(UserEntity::class);
        $this->repouser = RF::getInstanceOf(UserRepository::class)->setAppEntity($this->entityuser);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _check_entity_delete_permission(array $entity): void
    {
        $idUser = (int) $entity["id"];
        $idauthuser = (int) $this->authUserArray["id"];

        //si el logado quiere borrarse a si mismo
        if ($idauthuser === $idUser) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }

        if ($this->authService->isAuthUserRoot()) {
            return;
        }

        if ($this->authService->isAuthUserSysadmin()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        ) {
            return;
        }

        $identyowner = $this->repouser->getIdOwnerByIdUser($idUser);
        //si el usuario logado es owner y quiere eliminar un manager que le pertenece
        if ($this->authService->isAuthUserBusinessOwner()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_MANAGER])
            && $idauthuser === $identyowner
        ) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _check_entity_undelete_permission(array $entity): void
    {
        $idUser = (int) $entity["id"];
        $idauthuser = (int) $this->authUserArray["id"];
        if ($idauthuser === $idUser) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }

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
        if (!$idUser = $this->repouser->getEntityIdByEntityUuid($entity["uuid"])) {
            $this->_throwException(__("Data not found"), ExceptionType::CODE_NOT_FOUND);
        }

        $entity["id"] = $idUser;
        if (!$this->entityuser->areAllPksPresent($entity)) {
            $this->_throwException(__("Not all keys provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($this->repouser->isDeletedByEntityId($idUser)) {
            $this->_throwException(
                __("This item is already deleted {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );
        }

        $entity = $this->repouser->getEntityByEntityId($idUser);
        $this->_check_entity_delete_permission($entity);

        $updatedate = $this->repouser->getSysUpdateDateByPkFields($entity);
        $this->entityuser->addSysDelete($entity, $updatedate, $this->authUserArray["id"]);
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
        if (!$idUser = $this->repouser->getEntityIdByEntityUuid($entity["uuid"])) {
            $this->_throwException(__("Data not found"), ExceptionType::CODE_NOT_FOUND);
        }

        $entity["id"] = $idUser;
        if (!$this->entityuser->areAllPksPresent($entity)) {
            $this->_throwException(__("Not all keys provided"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$this->repouser->isDeletedByEntityId($idUser)) {
            $this->_throwException(
                __("Is not possible to restore entity {0}", $entity["uuid"]),
                ExceptionType::CODE_NOT_ACCEPTABLE
            );
        }

        $entity = $this->repouser->getEntityByEntityId($idUser);
        $this->_check_entity_undelete_permission($entity);
        $idauthuser = $this->authUserArray["id"];

        $entity = [
            "uuid" => $entity["uuid"],
            "id" => $idUser,
            "delete_date" => null,
            "delete_user" => null,
            "delete_platform" => null,
            "cru_csvnote" => $this->repouser->getCsvCru($entity, $idauthuser),
        ];

        $this->entityuser->addSysUpdate($entity, $idauthuser);
        $affected = $this->repouser->update($entity);

        return [
            "affected" => $affected,
            "uuid" => $entity["uuid"]
        ];
    }
}
