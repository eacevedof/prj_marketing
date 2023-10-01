<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use TheFramework\Components\Session\ComponentEncDecrypt;
use App\Restrict\Users\Domain\Events\UserWasCreatedEvent;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Restrict\Users\Domain\{UserEntity, UserRepository};
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Shared\Infrastructure\Factories\{
    EntityFactory as MF,
    RepositoryFactory as RF,
    ServiceFactory as SF
};

final class UsersInsertService extends AppService implements IEventDispatcher
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;
    private ComponentEncDecrypt $componentEncDecrypt;
    private UserRepository $userRepository;
    private FieldsValidator $fieldsValidator;
    private UserEntity $userEntity;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();
        $this->input = $input;

        $this->userEntity = MF::getInstanceOf(UserEntity::class);
        $this->userRepository = RF::getInstanceOf(UserRepository::class)->setAppEntity($this->userEntity);
        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->componentEncDecrypt = $this->_getEncDecryptInstance();
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

    private function _getConfiguredFieldsValidator(): FieldsValidator
    {
        $this->fieldsValidator
            ->addSkipableField("password2")
        ;
        $this->fieldsValidator
            ->addRule("email", "email", function ($data) {
                $value = $data["value"] ?? "";
                return $this->userRepository->getUserIdByEmail($value) ? __("This email already exists") : false;
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
            ->addRule("password", "not-equal", function ($data) {
                $value = $data["value"] ?? "";
                return ($value === ($data["data"]["password2"] ?? "")) ? false : __("Bad password confirmation");
            })
            ->addRule("password", "empty", function ($data) {
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
        ;
        return $this->fieldsValidator;
    }

    private function _dispatchEvents(array $payload): void
    {
        EventBus::instance()->publish(...[
            UserWasCreatedEvent::fromPrimitives($payload["id"], $payload["user"])
        ]);
    }

    public function __invoke(): array
    {
        $user = $this->_getRequestWithoutOperations($this->input);
        if (!$user) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->fieldsValidator = VF::getFieldValidator($user, $this->userEntity);
        if ($errors = $this->_getConfiguredFieldsValidator()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $userToCreate = $this->userEntity->getAllKeyValueFromRequest($user);
        $userToCreate["secret"] = $this->componentEncDecrypt->getPasswordHashed($userToCreate["secret"]);
        $userToCreate["description"] = $userToCreate["fullname"];
        $userToCreate["uuid"] = uniqid();
        $this->userEntity->addSysInsert($userToCreate, $this->authUserArray["id"]);

        //save user
        $idUser = $this->userRepository->insert($userToCreate);
        $userCreated = $this->userRepository->getEntityByEntityId((string) $idUser);

        $this->_dispatchEvents(["id" => $idUser, "user" => $userCreated]);
        return [
            "id" => $idUser,
            "uuid" => $userCreated["uuid"]
        ];
    }
}
