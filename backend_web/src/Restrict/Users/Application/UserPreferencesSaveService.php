<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserPreferenceType};
use App\Restrict\Users\Domain\{UserPreferencesEntity, UserPreferencesRepository, UserRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserPreferencesSaveService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;

    private UserRepository $userRepository;
    private ArrayRepository $arrayRepository;
    private UserPreferencesRepository $userPreferencesRepository;
    private FieldsValidator $fieldsValidator;
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
        $this->arrayRepository = RF::getInstanceOf(ArrayRepository::class);
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

    private function _checkEntityPermissionOrFail(int $userPreferenceId): void
    {
        if ($userPreferenceId) {
            if (!$userPreferenceId = $this->userPreferencesRepository->getUserPreferenceByIdUserAndIdUserPreference($userPreferenceId, $this->idUser)) {
                $this->_throwException(
                    __("{0} {1} does not exist", __("Preference"), $userPreferenceId),
                    ExceptionType::CODE_BAD_REQUEST
                );
            }
        }

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
        if ($this->authService->isAuthUserBusinessOwner()
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

    private function _skip_validation_insert(): self
    {
        $this->fieldsValidator
            ->addSkipableField("id")
            ->addSkipableField("id_user")
        ;
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->fieldsValidator
            ->addRule("id", "id", function ($data) {
                if ($data["data"]["_new"]) {
                    return false;
                }
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("pref_key", "pref_key", function ($data) {
                if (!$prefkey = $data["value"]) {
                    __("Empty field is not allowed");
                }
                if (!in_array($prefkey, ($keys = [UserPreferenceType::URL_DEFAULT_MODULE, UserPreferenceType::KEY_TZ]))) {
                    return __("Invalid key. Valid values are<br/>{0}", implode("<br/>", $keys));
                }
                if ($data["data"]["_new"] && $this->userPreferencesRepository->getUserPreferenceIdByIdUserAndPrefKey($this->idUser, $prefkey)) {
                    return __("{0} already exists", $prefkey);
                }
                if (!$data["data"]["_new"] && ($this->userPreferencesRepository->getUserPreferenceIdByIdUserAndPrefKey($this->idUser, $prefkey) !== (int) $data["data"]["id"])) {
                    return __("{0} already exists", $prefkey);
                }
                return false;
            })
            ->addRule("pref_value", "pref_value", function ($data) {
                if (!$prefvalue = $data["value"]) {
                    return __("Empty field is not allowed");
                }

                if ($data["data"]["pref_key"] === UserPreferenceType::KEY_TZ) {
                    if (!$this->arrayRepository->getTimezoneIdByDescription($prefvalue)) {
                        $zones = $this->arrayRepository->getTimezones();
                        unset($zones[0]);
                        $zones = implode("<br/>", array_column($zones, "value"));
                        return __("Invalid timezone. Valid are: {0}", $zones);
                    }
                }
                return false;
            });
        return $this->fieldsValidator;
    }

    private function _insert(array $prefreq): array
    {
        $this->fieldsValidator = VF::getFieldValidator($prefreq, $this->userPreferencesEntity);
        if ($errors = $this->_skip_validation_insert()->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $prefreq["id_user"] = $this->idUser;
        $prefreq = $this->userPreferencesEntity->getAllKeyValueFromRequest($prefreq);
        $this->userPreferencesEntity->addSysInsert($prefreq, $this->authUserArray["id"]);
        $this->userPreferencesRepository->insert($prefreq);
        $result = $this->userPreferencesRepository->getUserPreferenceByIdUser($this->idUser);

        return array_map(function ($row) {
            return [
                "id" => (int) $row["id"],
                "pref_key" => $row["pref_key"],
                "pref_value" => $row["pref_value"],
            ];
        }, $result);
    }

    private function _update(array $prefreq): array
    {
        $this->fieldsValidator = VF::getFieldValidator($prefreq, $this->userPreferencesEntity);
        if ($errors = $this->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $prefreq = $this->userPreferencesEntity->getAllKeyValueFromRequest($prefreq);
        $this->_checkEntityPermissionOrFail((int) $prefreq["id"]);
        $this->userPreferencesEntity->addSysUpdate($prefreq, $this->authUserArray["id"]);
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

        $prefreq["_new"] = true;
        if ($id = (int) ($prefreq["id"] ?? "")) {
            $prefreq["_new"] = false;
        }

        return $id
            ? $this->_update($prefreq)
            : $this->_insert($prefreq);
    }
}
