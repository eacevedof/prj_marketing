<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Application\Dtos\UserUpdateDto;
use TheFramework\Components\Session\ComponentEncDecrypt;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Restrict\Users\Domain\{UserEntity, UserRepository};
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\{
    EntityFactory as MF,
    RepositoryFactory as RF,
    ServiceFactory as SF
};

final class UsersUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private ComponentEncDecrypt $componentEncDecrypt;
    private UserRepository $userRepository;
    private UserEntity $userEntity;
    private UserUpdateDto $userUpdateDto;

    public function __construct()
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->userEntity = MF::getInstanceOf(UserEntity::class);
        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        $this->userRepository->setAppEntity($this->userEntity);
        $this->componentEncDecrypt = $this->_getEncDecryptInstance();
    }

    public function __invoke(UserUpdateDto $userUpdateDto): array
    {
        $this->userUpdateDto = $userUpdateDto;
        if (!$uuid = $this->userUpdateDto->uuid()) {
            $this->_throwException(__("Empty required code"), ExceptionType::CODE_BAD_REQUEST);
        }

        if (!$idUser = $this->userRepository->getEntityIdByEntityUuid($uuid)) {
            $this->_throwException(__("{0} with code {1} not found", __("User"), $uuid), 404);
        }

        if ($errors = $this->_getErrorsAfterRequestValidation()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $userEntityToUpdate = $this->userEntity->getAllKeyValueFromRequest($this->userUpdateDto);
        $userEntityToUpdate["id"] = $idUser;

        $this->_checkEntityPermissionOrFail($userEntityToUpdate);
        $this->_handlePasswordChange($userEntityToUpdate);

        $userEntityToUpdate["description"] = $userEntityToUpdate["fullname"];
        $this->userEntity->addSysUpdate($userEntityToUpdate, $this->authService->getAuthUserArray()["id"]);

        $affected = $this->userRepository->update($userEntityToUpdate);
        return [
            "affected" => $affected,
            "uuid" => $userEntityToUpdate["uuid"]
        ];
    }

    private function _handlePasswordChange(array &$userEntityToUpdate): void
    {
        if (!$passwordConfirmation = $this->userUpdateDto->secret2()) {
            unset($userEntityToUpdate["secret"], $userEntityToUpdate["secret2"]);
            return;
        }
        $userEntityToUpdate["secret"] = $this->componentEncDecrypt->getPasswordHashed($passwordConfirmation);
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

    private function _checkEntityPermissionOrFail(array $userEntity): void
    {
        $idUser = $this->userRepository->getEntityIdByEntityUuid($userEntity["uuid"]);
        $idAuthUser = (int) $this->authService->getAuthUserArray()["id"];
        if ($this->authService->isAuthUserRoot() || $idAuthUser === $idUser) {
            return;
        }

        if ($this->authService->isAuthUserSysadmin()
            && in_array($userEntity["id_profile"], [UserProfileType::BUSINESS_OWNER, UserProfileType::BUSINESS_MANAGER])
        ) {
            return;
        }

        $idEntityOwner = $this->userRepository->getIdOwnerByIdUser($idUser);
        //si logado es propietario y el bm a modificar le pertenece
        if ($this->authService->isAuthUserBusinessOwner()
            && $userEntity["id_profile"] === UserProfileType::BUSINESS_MANAGER
            && $idAuthUser === $idEntityOwner
        ) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _getErrorsAfterRequestValidation(): array
    {
        $validator = VF::getFieldValidatorFromDto($this->userUpdateDto, $this->userEntity);

        //@deuda esto hay que llevarlo a un servicio de validación. Con el DTO ya no hace falta comprobar
        //la información sobrante.
        $validator->addSkipableField("secret2")
            ->addSkipableField("secret");

        $validator
            ->addRule("email", "email", function ($data) {
                $email = $data["value"] ?? "";
                $uuid = $data["data"]["uuid"] ?? "";
                $idUserByUuid = $this->userRepository->getEntityIdByEntityUuid($uuid);
                if (!$idUserByUuid) {
                    return __("{0} with code {1} not found", __("User"), $uuid);
                }
                $idUserByEmail = $this->userRepository->getUserIdByEmail($email);
                if (!$idUserByEmail || ($idUserByUuid == $idUserByEmail)) {
                    return false;
                }
                return __("This email already exists");
            })
            ->addRule("email", "email", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("email", "email", function ($data) {
                $value = $data["value"] ?? "";
                return filter_var($value, FILTER_VALIDATE_EMAIL) ? false : __("Invalid email format");
            })
            ->addRule("phone", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("fullname", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("birthdate", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("id_profile", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("id_parent", "by-profile", function ($data) {
                $value = $data["value"] ?? "";
                if (($data["data"]["id_profile"] ?? "") === "4" && !$value) {
                    return __("Empty field is not allowed");
                }
                return false;
            })
            ->addRule("id_country", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("id_language", "empty", function ($data) {
                $value = $data["value"] ?? "";
                return $value ? false : __("Empty field is not allowed");
            })
            ->addRule("password", "not-equal", function () {
                if (!$password2 = $this->userUpdateDto->secret2()) {
                    return false;
                }
                if (!$password = $this->userUpdateDto->secret()) {
                    return __("Bad password confirmation");
                }
                return ($password !== $password2) ? __("Bad password confirmation") : false;
            })
        ;
        return $validator->getErrors();
    }

}
