<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\Promotions\Domain\{PromotionRepository, PromotionUiEntity, PromotionUiRepository};
use App\Shared\Infrastructure\Factories\{EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionUiUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;

    private PromotionRepository $repopromotion;
    private PromotionUiRepository $repopromotionui;
    private FieldsValidator $validator;
    private PromotionUiEntity $entitypromotionui;
    private int $idpromotion;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        if (!$promouuid = $this->input["_promotionuuid"]) {
            $this->_throwException(__("No {0} code provided", __("user")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class);
        if (!$promotion = $this->repopromotion->getEntityByEntityUuid($promouuid)) {
            $this->_throwException(__("{0} with code {1} not found", __("Promotion"), $promouuid));
        }

        if ($this->repopromotion->doesPromotionHaveSubscribersByPromotionUuid($promouuid)) {
            $this->_throwException(__("{0} with code {1} is not editable", __("Promotion"), $promouuid));
        }

        $this->idpromotion = $promotion["id"];
        $this->entitypromotionui = MF::getInstanceOf(PromotionUiEntity::class);
        $this->repopromotionui = RF::getInstanceOf(PromotionUiRepository::class)->setAppEntity($this->entitypromotionui);
        $this->authUserArray = $this->authService->getAuthUserArray();
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_UI_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(): void
    {
        //si es super puede interactuar con la entidad
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        //un root puede cambiar la entidad de cualquiera
        if ($this->authService->isAuthUserRoot()) {
            return;
        }

        //un sysadmin puede cambiar los de cualquiera
        if ($this->authService->isAuthUserSysadmin()) {
            return;
        }

        $identowner = (int) $this->repopromotion->getEntityByEntityId($this->idpromotion)["id_owner"];
        //si es bow o bm y su idwoner es el de la ui
        if ($this->authService->getIdOwner() === $identowner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _add_rules(): FieldsValidator
    {
        $fn_isvalidbool = function (string $value) {
            return in_array($value, ["0", "1"]);
        };

        $fn_validint = function (string $value) {
            $value = (int) $value;
            return ($value > -1 && $value < 1000);
        };

        $this->validator
            ->addRule("id", "id", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("uuid", "uuid", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("id_owner", "id_owner", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("input_email", "input_email", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_email", "pos_email", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_gender", "input_gender", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_gender", "pos_gender", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_language", "input_language", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_language", "pos_language", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_name1", "input_name1", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_name1", "pos_name1", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_name2", "input_name2", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_name2", "pos_name2", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_phone1", "input_phone1", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_phone1", "pos_phone1", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_address", "input_address", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_address", "pos_address", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_birthdate", "input_birthdate", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_birthdate", "pos_birthdate", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_country", "input_country", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_country", "pos_country", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_is_mailing", "input_is_mailing", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_is_mailing", "pos_is_mailing", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
            ->addRule("input_is_terms", "input_is_terms", function ($data) use ($fn_isvalidbool) {
                return ($fn_isvalidbool($data["value"])) ? false : __("Unrecognized value for this field");
            })
            ->addRule("pos_is_terms", "pos_is_terms", function ($data) use ($fn_validint) {
                return ($fn_validint($data["value"])) ? false : __("Valid values are 1-100");
            })
        ;

        return $this->validator;
    }

    private function _remove_readonly(array &$promotionui): void
    {
        $remove = [
            "uuid", "id_owner", "id_promotion", "input_email"
        ];
        foreach ($remove as $field) {
            unset($promotionui[$field]);
        }
    }

    private function _update(array $promouireq, array $promotionui): array
    {
        if (((int) $promouireq["id"]) !== $promotionui["id"]) {
            $this->_throwException(
                __("This promotion UI does not belong to promotion {0}", $this->input["_promotionuuid"]),
                ExceptionType::CODE_BAD_REQUEST
            );
        }

        if ($errors = $this->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $promouireq = $this->entitypromotionui->getAllKeyValueFromRequest($promouireq);
        $this->_checkEntityPermissionOrFail();
        $this->entitypromotionui->addSysUpdate($promouireq, $this->authUserArray["id"]);
        $this->_remove_readonly($promouireq);
        $this->repopromotionui->update($promouireq);
        return [
            "id" => $promotionui["id"],
            "uuid" => $promotionui["uuid"]
        ];
    }

    public function __invoke(): array
    {
        if (!$promouireq = $this->_getRequestWithoutOperations($this->input)) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->_checkEntityPermissionOrFail();
        $this->validator = VF::getFieldValidator($promouireq, $this->entitypromotionui);

        $promotionui = $this->repopromotionui->getPromotionUiByIdPromotion($this->idpromotion);
        if (!$promotionui) {
            $this->_throwException(__("{0} not found!", __("Promotion UI")), ExceptionType::CODE_NOT_FOUND);
        }

        return $this->_update($promouireq, $promotionui);
    }
}
