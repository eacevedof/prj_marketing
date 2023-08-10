<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};
use App\Restrict\Users\Domain\{UserPreferencesEntity, UserPreferencesRepository, UserRepository};

final class UserPreferencesListService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;

    private UserRepository $userRepository;
    private UserPreferencesRepository $userPreferencesRepository;
    private UserPreferencesEntity $userPreferencesEntity;
    private int $idUser;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"]) {
            $this->_throwException(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        if (!$this->idUser = $this->userRepository->getEntityIdByEntityUuid($useruuid)) {
            $this->_throwException(__("{0} with code {1} not found", __("User"), $useruuid));
        }

        $this->userPreferencesRepository = RF::getInstanceOf(UserPreferencesRepository::class);
        $this->authUserArray = $this->authService->getAuthUserArray();
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if(
            $this->authService->hasAuthUserPolicy(UserPolicyType::USER_PREFERENCES_READ) ||
            $this->authService->hasAuthUserPolicy(UserPolicyType::USER_PREFERENCES_WRITE)
        ) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _checkEntityPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot() || $this->authService->isAuthUserRoot()) {
            return;
        }

        $userOfPreference = $this->userRepository->getEntityByEntityId($this->idUser);
        $idAuthUser = (int) $this->authUserArray["id"];
        if ($idAuthUser === $this->idUser) {
            return;
        }

        if ($this->authService->isAuthUserSysadmin() && $this->authService->isIdProfileBusinessProfile($userOfPreference["id_profile"])) {
            return;
        }

        $idOwnerOfEntity = $this->userRepository->getIdOwnerByIdUser($this->idUser);
        //si logado es propietario y el bm a modificar le pertenece
        if (
            $this->authService->isAuthUserBusinessOwner()
            && $this->authService->isIdProfileBusinessProfile($userOfPreference["id_profile"])
            && $idAuthUser === $idOwnerOfEntity
        ) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        $this->_checkEntityPermissionOrFail();

        $result = $this->userPreferencesRepository->getUserPreferenceByIdUser($this->idUser);
        return array_map(function ($row) {
            return [
                "id" => (int) $row["id"],
                "pref_key" => $row["pref_key"],
                "pref_value" => $row["pref_value"],
            ];
        }, $result);
    }
}
