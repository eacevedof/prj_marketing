<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Checker\Application\CheckerService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\BusinessData\Domain\{BusinessDataEntity, BusinessDataRepository};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserBusinessDataSaveService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;

    private UserRepository $userRepository;
    private BusinessDataRepository $businessDataRepository;
    private FieldsValidator $fieldsValidator;
    private BusinessDataEntity $businessDataEntity;
    private int $idUserOfBusinessData;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        if (!$useruuid = $this->input["_useruuid"]) {
            $this->_throwException(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        if (!$this->idUserOfBusinessData = $this->userRepository->getEntityIdByEntityUuid($useruuid)) {
            $this->_throwException(__("{0} with code {1} not found", __("User"), $useruuid));
        }

        $this->businessDataEntity = MF::getInstanceOf(BusinessDataEntity::class);
        $this->businessDataRepository = RF::getInstanceOf(BusinessDataRepository::class)->setAppEntity($this->businessDataEntity);
        $this->authUserArray = $this->authService->getAuthUserArray();
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::BUSINESSDATA_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->authService->hasAuthUserSystemProfile()) {
            return;
        }

        //si el us en sesion quiere cambiar su bd
        $idAuthUser = (int) $this->authUserArray["id"];
        if ($idAuthUser === $this->idUserOfBusinessData) {
            return;
        }

        //si el propietario del us de sesion coincide con el de la entidad
        if ($this->authService->getIdOwner() === $this->idUserOfBusinessData) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _skipValidationFields(): self
    {
        $this->fieldsValidator
            ->addSkipableField("id")
            ->addSkipableField("uuid")
            ->addSkipableField("id_user")
            ->addSkipableField("slug")
        ;
        return $this;
    }

    private function _addRulesToFieldsValidator(): FieldsValidator
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
            ->addRule("id_tz", "id_tz", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("business_name", "business_name", function ($data) {
                if (!$data["data"]["_new"]) {
                    return false;
                }
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("user_logo_1", "user_logo_1", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("user_logo_2", "user_logo_2", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("user_logo_3", "user_logo_3", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })

            ->addRule("url_favicon", "url_favicon", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })

            ->addRule("head_bgcolor", "head_bgcolor", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidColor($value) ? __("Invalid hex color") : false;
            })
            ->addRule("head_color", "head_color", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidColor($value) ? __("Invalid hex color") : false;
            })
            ->addRule("head_bgimage", "head_bgimage", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("body_bgcolor", "body_bgcolor", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidColor($value) ? __("Invalid hex color") : false;
            })
            ->addRule("body_color", "body_color", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidColor($value) ? __("Invalid hex color") : false;
            })
            ->addRule("body_bgimage", "body_bgimage", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("url_business", "url_business", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("url_social_fb", "url_social_fb", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("url_social_ig", "url_social_ig", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("url_social_twitter", "url_social_twitter", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
            ->addRule("url_social_tiktok", "url_social_tiktok", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                return !CheckerService::isValidUrl($value) ? __("Invalid url value") : false;
            })
        ;

        return $this->fieldsValidator;
    }

    private function _update(array $businessDataToUpdate, array $dbBusinessData): array
    {
        unset($businessDataToUpdate["business_name"]);
        if ($dbBusinessData["id"] !== $businessDataToUpdate["id"]) {
            $this->_throwException(
                __("This {0} does not belong to user {1}", __("Business data"), $this->input["_useruuid"]),
                ExceptionType::CODE_BAD_REQUEST
            );
        }

        //no se hace skip pq se tiene que cumplir todo
        if ($errors = $this->_addRulesToFieldsValidator()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $businessDataToUpdate = $this->businessDataEntity->getAllKeyValueFromRequest($businessDataToUpdate);
        $this->_checkEntityPermissionOrFail();
        $this->businessDataEntity->addSysUpdate($businessDataToUpdate, $this->authUserArray["id"]);
        $this->businessDataRepository->update($businessDataToUpdate);
        return [
            "id" => $dbBusinessData["id"],
            "uuid" => $businessDataToUpdate["uuid"]
        ];
    }

    private function _insert(array $update): array
    {
        $update["_new"] = true;
        $this->fieldsValidator = VF::getFieldValidator($update, $this->businessDataEntity);
        if ($errors = $this->_skipValidationFields()->_addRulesToFieldsValidator()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }
        $update["id_user"] = $this->idUserOfBusinessData;
        $update["uuid"] = uniqid();
        $update["slug"] = CF::getInstanceOf(TextComponent::class)->getSlug($update["business_name"])."-$this->idUserOfBusinessData";
        $update = $this->businessDataEntity->getAllKeyValueFromRequest($update);
        $this->businessDataEntity->addSysInsert($update, $this->authUserArray["id"]);
        $id = $this->businessDataRepository->insert($update);
        return [
            "id" => $id,
            "uuid" => $update["uuid"],
            "slug" => $update["slug"]
        ];
    }

    public function __invoke(): array
    {
        unset($this->input["slug"]);
        if (!$businessDataToUpdate = $this->_getRequestWithoutOperations($this->input)) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->_checkEntityPermissionOrFail();

        $businessDataToUpdate["_new"] = false;
        $this->fieldsValidator = VF::getFieldValidator($businessDataToUpdate, $this->businessDataEntity);

        return ($dbBusinessData = $this->businessDataRepository->getBusinessDataByIdUser($this->idUserOfBusinessData))
            ? $this->_update($businessDataToUpdate, $dbBusinessData)
            : $this->_insert($businessDataToUpdate);
    }
}
