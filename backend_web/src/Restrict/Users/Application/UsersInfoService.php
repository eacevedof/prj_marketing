<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Restrict\BusinessAttributes\Domain\BusinessAttributeRepository;
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};
use App\Restrict\Users\Domain\{UserPermissionsRepository, UserPreferencesRepository, UserRepository};

final class UsersInfoService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;
    private UserRepository $userRepository;
    private UserPermissionsRepository $userPermissionRepository;
    private UserPreferencesRepository $userPreferencesRepository;
    private BusinessDataRepository $businessDataRepository;
    private BusinessAttributeRepository $businessAttributeRepository;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        if (!$this->input = $input[0] ?? "") {
            $this->_throwException(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        $this->userPermissionRepository = RF::getInstanceOf(UserPermissionsRepository::class);
        $this->userPreferencesRepository = RF::getInstanceOf(UserPreferencesRepository::class);
        $this->businessDataRepository = RF::getInstanceOf(BusinessDataRepository::class);
        $this->businessAttributeRepository = RF::getInstanceOf(BusinessAttributeRepository::class);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::USERS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE)
        )) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(array $entity): void
    {
        $idUser = (int) $entity["id"];
        $idAuthUser = (int) $this->authUserArray["id"];
        if ($this->authService->isAuthUserRoot() || $idAuthUser === $idUser) {
            return;
        }

        if ($this->authService->isAuthUserSysadmin()
            && in_array($entity["id_profile"], [UserProfileType::SYS_ADMIN, UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        ) {
            return;
        }

        $idEntityOwner = $this->userRepository->getIdOwnerByIdUser($idUser);
        //si logado es propietario del bm
        if ($this->authService->isAuthUserBusinessOwner()
            && in_array($entity["id_profile"], [UserProfileType::BUSINESS_MANAGER])
            && $idAuthUser === $idEntityOwner
        ) {
            return;
        }

        //si el logado es bm y la ent es del mismo owner
        $idAuthOwner = $this->userRepository->getIdOwnerByIdUser($idAuthUser);
        if ($this->authService->hasAuthUserBusinessManagerProfile() && $idAuthOwner === $idEntityOwner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        if (!$user = $this->userRepository->getUserInfoByUserUuid($this->input)) {
            $this->_throwException(
                __("{0} with code {1} not found", __("User"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        //comprueba propiedad de la entidad
        $this->_checkEntityPermissionOrFail($user);

        $isUserPermissionsAllowed = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_USER_PERMISSIONS,
            UserPolicyType::READ
        )[0];

        $isBusinessDataAllowed = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_BUSINESSDATA,
            UserPolicyType::READ
        )[0] && $this->authService->isAuthUserBusinessOwner($user["id_profile"]);

        $isUserPreferencesAllowed = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_USER_PREFERENCES,
            UserPolicyType::READ
        )[0];

        $idUser = $user["id"];
        return [
            "user" => $this->_getRowWithSysDataByTz($user, $tz = $this->authService->getAuthUserTZ()),
            "permissions" => $isUserPermissionsAllowed
                ? $this->_getRowWithSysDataByTz($this->userPermissionRepository->getUserPermissionByIdUser($idUser), $tz)
                : null,
            "businessdata" => $isBusinessDataAllowed
                ? $this->_getRowWithSysDataByTz($this->businessDataRepository->getBusinessDataByIdUser($idUser), $tz)
                : null,
            "businessattributespace" => $isBusinessDataAllowed
                ? $this->_getRowWithSysDataByTz($this->businessAttributeRepository->getSpacePageByIdUser($idUser), $tz)
                : null,
            "preferences" => $isUserPreferencesAllowed ? $this->userPreferencesRepository->getUserPreferenceByIdUser($idUser) : null,
        ];
    }

    public function getUsersInfoForEdition(): array
    {
        if (!$user = $this->userRepository->getEntityByEntityUuid($this->input)) {
            $this->_throwException(
                __("User with code {0} not found", $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        //comprueba propiedad de la entidad
        $this->_checkEntityPermissionOrFail($user);

        $isUserPermissionAllowed = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_USER_PERMISSIONS,
            UserPolicyType::WRITE
        )[0];

        $isBusinessDataAllowed = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_BUSINESSDATA,
            UserPolicyType::WRITE
        )[0] && $this->authService->isAuthUserBusinessOwner($user["id_profile"]);

        $isUserPreferencesAllowed = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_USER_PREFERENCES,
            UserPolicyType::WRITE
        )[0];

        $idUser = $user["id"];
        return [
            "user" => $this->_getRowWithSysDataByTz($user, $tz = $this->authService->getAuthUserTZ()),
            "permissions" => $isUserPermissionAllowed
                ? $this->_getRowWithSysDataByTz($this->userPermissionRepository->getUserPermissionByIdUser($idUser), $tz)
                : null,
            "businessdata" => $isBusinessDataAllowed
                ? $this->_getRowWithSysDataByTz($this->businessDataRepository->getBusinessDataByIdUser($idUser), $tz)
                : null,
            "businessattributespace" => $isBusinessDataAllowed
                ? $this->_getRowWithSysDataByTz($this->businessAttributeRepository->getSpacePageByIdUser($idUser), $tz)
                : null,
            "preferences" => $isUserPreferencesAllowed ? $this->userPreferencesRepository->getUserPreferenceByIdUser($idUser) : null,
        ];
    }
}
