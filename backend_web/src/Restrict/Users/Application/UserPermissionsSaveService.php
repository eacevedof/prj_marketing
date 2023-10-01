<?php

namespace App\Restrict\Users\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Domain\Enums\{ExceptionType, SystemUserIdTypes};
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\UserPermissions\Application\Dtos\UserPermissionsSaveDto;
use App\Restrict\Users\Domain\{UserPermissionsEntity, UserPermissionsRepository, UserRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserPermissionsSaveService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;
    private UserRepository $userRepository;
    private UserPermissionsRepository $userPermissionsRepository;
    private FieldsValidator $fieldsValidator;
    private UserPermissionsEntity $userPermissionsEntity;
    private int $userIdOfPermission;
    private UserPermissionsSaveDto $userPermissionsSaveDto;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->userPermissionsEntity = MF::getInstanceOf(UserPermissionsEntity::class);

        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        $this->userPermissionsRepository = RF::getInstanceOf(UserPermissionsRepository::class);
        $this->userPermissionsRepository->setAppEntity($this->userPermissionsEntity);

        $this->authUserArray = $this->authService->getAuthUserArray();
    }

    public function __invoke(UserPermissionsSaveDto $userPermissionsSaveDto): array
    {
        $this->_failIfWrongProfile();

        $this->userPermissionsSaveDto = $userPermissionsSaveDto;

        if (!$userUuid = $this->userPermissionsSaveDto->userUuid()) {
            $this->_throwException(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$this->userIdOfPermission = $this->userRepository->getEntityIdByEntityUuid($userUuid)) {
            $this->_throwException(__("{0} with code {1} not found", __("User"), $userUuid));
        }

        if ($this->userIdOfPermission === SystemUserIdTypes::SUPER_ROOT_ID) {
            $this->_throwException(__("You can not add permissions to this user"));
        }

        $this->failIfAuthUserHasNoPermissionsOverThisEntity();

        $updatePermission = [
            "_new" => false,
            "id" => $this->userPermissionsSaveDto->id(),
            "uuid" => $this->userPermissionsSaveDto->uuid(),
            "id_user" => $this->userPermissionsSaveDto->idUser(),
            "json_rw" => $this->userPermissionsSaveDto->jsonRw(),
        ];

        $this->fieldsValidator = VF::getFieldValidator($updatePermission, $this->userPermissionsEntity);

        return ($dbPermissions = $this->userPermissionsRepository->getUserPermissionByIdUser($this->userIdOfPermission))
            ? $this->_updateOrFail($updatePermission, $dbPermissions)
            : $this->_insertOrFail($updatePermission);
    }

    private function _failIfWrongProfile(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::USER_PERMISSIONS_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function failIfAuthUserHasNoPermissionsOverThisEntity(): void
    {
        //el usuario al que se le va a modificar los permisos
        $userOfThisPermissions = $this->userRepository->getEntityByEntityId($this->userIdOfPermission);
        $idAuthUser = (int) $this->authUserArray["id"];
        if ($idAuthUser === $this->userIdOfPermission) {
            $this->_throwException(
                __("You are not allowed to change your own permissions"),
                ExceptionType::CODE_FORBIDDEN
            );
        }

        //un root puede cambiar el de cualquiera (menos el de el mismo, if anterior)
        if ($this->authService->isAuthUserRoot()) {
            return;
        }

        //un sysadmin puede cambiar solo a los que tiene debajo
        if (
            $this->authService->isAuthUserSysadmin() &&
            $this->authService->isIdProfileBusinessProfile($userOfThisPermissions["id_profile"])
        ) {
            return;
        }

        $idEntityOwner = $this->userRepository->getIdOwnerByIdUser($this->userIdOfPermission);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->authService->isAuthUserBusinessOwner()
            && $this->authService->isIdProfileBusinessProfile($userOfThisPermissions["id_profile"])
            && ((int) $this->authUserArray["id"]) === $idEntityOwner
        ) {
            return;
        }

        //to-do, solo se pueden agregar los permisos que tiene el owner ninguno más
        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _skipValidationFieldsOnInsert(): self
    {
        $this->fieldsValidator
            ->addSkipableField("id")
            ->addSkipableField("uuid")
            ->addSkipableField("id_user")
        ;
        return $this;
    }

    private function _getConfiguredFieldsValidator(): FieldsValidator
    {
        $this->fieldsValidator
            ->addRule("id", "id", function ($data) {
                if ($data["data"]["_new"]) {
                    return false;
                }
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("uuid", "uuid", function ($data) {
                if ($data["data"]["_new"]) {
                    return false;
                }
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("id_user", "id_user", function ($data) {
                if ($data["data"]["_new"]) {
                    return false;
                }
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("json_rw", "json_rw", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("json_rw", "valid_json", function ($data) {
                return $this->_isValidJson($data["value"]) ? false : __("Invalid Json document");
            })
            ->addRule("json_rw", "valid rules", function ($data) {
                $values = json_decode($data["value"], 1);
                if (!$values) {
                    return false;
                }

                $allPolicies = UserPolicyType::getAllPolicies();
                $invalid = [];
                foreach ($values as $policy) {
                    if (!in_array($policy, $allPolicies)) {
                        $invalid[] = $policy;
                    }
                }
                if (!$invalid) {
                    return false;
                }
                $invalid = implode(", ", $invalid);
                //cuidado con esto. Un servicio no deberia deovlver html solo texto plano
                //para los casos en los que es consumido por otra interfaz
                $valid = "\"".implode("\",<br/>\"", $allPolicies)."\"";
                return __("Invalid policies: {0} <br/>Valid are:<br/>{1}", $invalid, $valid);
            })
        ;

        return $this->fieldsValidator;
    }

    private function _isValidJson(string $jsonString): bool
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function _updateOrFail(array $updatePermission, array $dbPermissions): array
    {
        if ($dbPermissions["id"] !== (int) $updatePermission["id"]) {
            $this->_throwException(
                __("This permission does not belong to user {0}", $this->userPermissionsSaveDto->userUuid()),
                ExceptionType::CODE_BAD_REQUEST
            );
        }

        //no se hace skip pq se tiene que cumplir todo
        if ($errors = $this->_getConfiguredFieldsValidator()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $updatePermission = $this->userPermissionsEntity->getAllKeyValueFromRequest($updatePermission);
        $this->failIfAuthUserHasNoPermissionsOverThisEntity();
        $this->userPermissionsEntity->addSysUpdate($updatePermission, $this->authUserArray["id"]);
        $this->userPermissionsRepository->update($updatePermission);
        return [
            "id" => $dbPermissions["id"],
            "uuid" => $updatePermission["uuid"]
        ];
    }

    private function _insertOrFail(array $update): array
    {
        $update["_new"] = true;
        $this->fieldsValidator = VF::getFieldValidator($update, $this->userPermissionsEntity);
        if ($errors = $this->_skipValidationFieldsOnInsert()->_getConfiguredFieldsValidator()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $update["id_user"] = $this->userIdOfPermission;
        $update["uuid"] = uniqid();
        $update = $this->userPermissionsEntity->getAllKeyValueFromRequest($update);
        $this->userPermissionsEntity->addSysInsert($update, $this->authUserArray["id"]);
        $id = $this->userPermissionsRepository->insert($update);
        return [
            "id" => $id,
            "uuid" => $update["uuid"]
        ];
    }
}
