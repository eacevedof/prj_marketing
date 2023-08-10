<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\{UserPermissionsRepository, UserRepository};
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};

final class UserPermissionsInfoService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;
    private UserRepository $repouser;
    private UserPermissionsRepository $repouserpermissions;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->repouser = RF::getInstanceOf(UserRepository::class);
        $this->repouserpermissions = RF::getInstanceOf(UserPermissionsRepository::class);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::USER_PERMISSIONS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::USER_PERMISSIONS_WRITE)
        )) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(int $idUser): void
    {
        if ($this->authService->isAuthUserRoot() || $this->authService->isAuthUserSysadmin()) {
            return;
        }

        $idauthuser = (int) $this->authUserArray["id"];
        $identowner = (int) $this->repouser->getIdOwnerByIdUser($idUser);

        //si el owner logado es propietario de la entidad
        if ($this->authService->isAuthUserBusinessOwner() && $idauthuser === $identowner) {
            return;
        }

        $idauthowner = $this->authService->getIdOwner();
        if ($this->authService->hasAuthUserBusinessManagerProfile() && $idauthowner === $identowner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function get_for_edit_by_user(string $uuid): array
    {
        if (!$id = $this->repouser->getEntityIdByEntityUuid($uuid)) {
            $this->_throwException("{0} with code {1} not found", __("User"), $uuid);
        }

        $this->_checkEntityPermissionOrFail($id);
        return $this->repouserpermissions->getUserPermissionByIdUser($id);
    }
}
