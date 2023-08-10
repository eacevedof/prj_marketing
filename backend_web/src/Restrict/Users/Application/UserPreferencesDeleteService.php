<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Domain\{UserPreferencesEntity, UserPreferencesRepository, UserRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserPreferencesDeleteService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;

    private UserRepository $userRepository;
    private UserPreferencesRepository $userPreferencesRepository;
    private FieldsValidator $validator;
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

        $this->userPreferencesEntity = MF::getInstanceOf(UserPreferencesEntity::class);
        $this->userPreferencesRepository = RF::getInstanceOf(UserPreferencesRepository::class)->setAppEntity($this->userPreferencesEntity);
        $this->authUserArray = $this->authService->getAuthUserArray();
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::USER_PREFERENCES_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(int $id): void
    {
        if (!$id = $this->userPreferencesRepository->getUserPreferenceByIdUserAndIdUserPreference($id, $this->idUser)) {
            $this->_throwException(
                __("{0} {1} does not exist", __("Preference"), $id),
                ExceptionType::CODE_BAD_REQUEST
            );
        }

        if ($this->authService->isAuthUserSuperRoot() || $this->authService->isAuthUserRoot()) {
            return;
        }

        $prefuser = $this->userRepository->getEntityByEntityId($this->idUser);
        $idauthuser = (int) $this->authUserArray["id"];
        if ($idauthuser === $this->idUser) {
            return;
        }

        if ($this->authService->isAuthUserSysadmin() && $this->authService->isIdProfileBusinessProfile($prefuser["id_profile"])) {
            return;
        }

        $identowner = $this->userRepository->getIdOwnerByIdUser($this->idUser);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->authService->isAuthUserBusinessOwner()
            && $this->authService->isIdProfileBusinessProfile($prefuser["id_profile"])
            && ((int) $this->authUserArray["id"]) === $identowner
        ) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _delete(array $prefreq): array
    {
        $prefreq = $this->userPreferencesEntity->getAllKeyValueFromRequest($prefreq);
        $this->_checkEntityPermissionOrFail((int) $prefreq["id"]);
        $updatedate = $this->userPreferencesRepository->getSysUpdateDateByPkFields($prefreq);
        $this->userPreferencesEntity->addSysDelete($prefreq, $updatedate, $this->authUserArray["id"]);
        $this->userPreferencesRepository->update($prefreq);
        $result = $this->userPreferencesRepository->getUserPreferenceByIdUser($this->idUser);

        return array_map(function ($row) {
            return [
                "id" => (int) $row["id"],
                "pref_key" => $row["pref_key"],
                "pref_value" => $row["pref_value"],
            ];
        }, $result);
    }

    public function __invoke(): array
    {
        if (!$prefreq = $this->_getRequestWithoutOperations($this->input)) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        return $this->_delete($prefreq);
    }
}
