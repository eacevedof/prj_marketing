<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\BusinessAttributes\Domain\{BusinessAttributeEntity, BusinessAttributeRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class UserBusinessAttributeSpaceSaveService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;

    private UserRepository $userRepository;
    private int $idUserOfBusinessData;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        if (!$useruuid = $input["_useruuid"]) {
            $this->_throwException(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->userRepository = RF::getInstanceOf(UserRepository::class);
        if (!$this->idUserOfBusinessData = $this->userRepository->getEntityIdByEntityUuid($useruuid)) {
            $this->_throwException(__("{0} with code {1} not found", __("User"), $useruuid));
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->_load_input($input);
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

    private function _load_input($input): void
    {
        $input = $this->_getRequestWithoutOperations($input);
        $this->input = [
            //"space_about_title" => $input["space_about_title"] ?? "",
            "space_about" => $input["space_about"] ?? "",
            //"space_plan_title" => $input["space_plan_title"] ?? "",
            "space_plan" => $input["space_plan"] ?? "",
            //"space_location_title" => $input["space_location_title"] ?? "",
            "space_location" => $input["space_location"] ?? "",
            //"space_contact_title" => $input["space_contact_title"] ?? "",
            "space_contact" => $input["space_contact"] ?? "",
        ];
    }

    private function _checkEntityPermissionOrFail(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->authService->hasAuthUserSystemProfile()) {
            return;
        }

        //si el us en sesion quiere cambiar su bd
        $idauthuser = (int) $this->authUserArray["id"];
        if ($idauthuser === $this->idUserOfBusinessData) {
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

    private function _validate(): array
    {
        $validator = VF::getFieldValidator($this->input);
        $validator
            ->addRule("space_about", "space_about", function ($data) {
                if (!$data["value"]) {
                    return __("Empty field is not allowed");
                }
            })
            ->addRule("space_plan", "space_plan", function ($data) {
                if (!$data["value"]) {
                    return __("Empty field is not allowed");
                }
            })
            ->addRule("space_location", "space_location", function ($data) {
                if (!$data["value"]) {
                    return __("Empty field is not allowed");
                }
            })
            ->addRule("space_contact", "space_contact", function ($data) {
                if (!$data["value"]) {
                    return __("Empty field is not allowed");
                }
            });

        return $validator->getErrors();
    }

    private function _update(): array
    {
        if ($errors = $this->_validate()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $this->_checkEntityPermissionOrFail();

        $tentitybusinessattrib = MF::getInstanceOf(BusinessAttributeEntity::class);
        $repobusinessattrib = RF::getInstanceOf(BusinessAttributeRepository::class)->setAppEntity($tentitybusinessattrib);
        $businessattribs = $repobusinessattrib->getSpacePageByIdUser($this->idUserOfBusinessData);
        $businessattribs = array_map(function (array $item) {
            $keys = array_keys($this->input);
            if (in_array($key = $item["attr_key"], $keys)) {
                $item["attr_value"] = $this->input[$key];
                return $item;
            }
            return null;
        }, $businessattribs);
        $businessattribs = array_filter($businessattribs);

        foreach ($businessattribs as $attrib) {
            $update = $tentitybusinessattrib->getAllKeyValueFromRequest($attrib);
            $tentitybusinessattrib->addSysUpdate($update, $this->authUserArray["id"]);
            $repobusinessattrib->update($update);
        }

        return $businessattribs;
    }

    public function __invoke(): array
    {
        $this->_checkEntityPermissionOrFail();
        return $this->_update();
    }
}
