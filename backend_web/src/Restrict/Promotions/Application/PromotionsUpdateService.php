<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Checker\Application\CheckerService;
use App\Restrict\Users\Domain\UserRepository;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\Promotions\Domain\{PromotionEntity, PromotionRepository};
use App\Shared\Infrastructure\Components\Date\{DateComponent, UtcComponent};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionsUpdateService extends AppService
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;
    private PromotionRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;
    private TextComponent $textformat;
    private DateComponent $datecomp;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->datecomp = CF::getInstanceOf(DateComponent::class);
        $this->_map_dates($input);
        $this->input = $this->_getRequestWithoutOperations($input);
        if (!$this->input["uuid"]) {
            $this->_throwException(__("Empty required code"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->entitypromotion = MF::getInstanceOf(PromotionEntity::class);
        $this->validator = VF::getFieldValidator($this->input, $this->entitypromotion);
        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class);
        $this->repopromotion->setAppEntity($this->entitypromotion);
        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->textformat = CF::getInstanceOf(TextComponent::class);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _map_dates(array &$input): void
    {
        $date = $input["date_from"] ?? "";
        $date = $this->datecomp->getDateInDbFormat00($date);
        $input["date_from"] = $date;

        $date = $input["date_to"] ?? "";
        $date = $this->datecomp->getDateInDbFormat00($date);
        $input["date_to"] = $date;

        $date = $input["date_execution"] ?? "";
        $date = $this->datecomp->getDateInDbFormat00($date);
        $input["date_execution"] = $date;

        $input["tags"] = CF::getInstanceOf(TextComponent::class)->getBlanksAsNull($input["tags"]);
    }

    private function _checkEntityPermissionOrFail(array $promotion): void
    {
        if (!$this->repopromotion->getEntityIdByEntityUuid($uuid = $promotion["uuid"])) {
            $this->_throwException(
                __("{0} {1} does not exist", __("Promotion"), $uuid),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        if ($this->authService->hasAuthUserSystemProfile()) {
            return;
        }

        $idauthuser = (int) $this->authUserArray["id"];
        $identowner = (int) $promotion["id_owner"];
        //si el logado es propietario de la promocion
        if ($idauthuser === $identowner) {
            return;
        }
        //si el logado tiene el mismo owner que la promo
        if (RF::getInstanceOf(UserRepository::class)->getIdOwnerByIdUser($idauthuser) === $identowner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->addRule("id", "id", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("uuid", "uuid", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("id_owner", "id_owner", function ($data) {
                //si no es de sistemas este campo no se puede cambiar
                if (!$this->authService->hasAuthUserSystemProfile()) {
                    return false;
                }
                if (!($value = $data["value"])) {
                    return __("Empty field is not allowed");
                }
                if (!RF::getInstanceOf(UserRepository::class)->isIdUserEnabledBusinessOwner((int) $value)) {
                    return __("Invalid owner");
                }
                return false;
            })
            ->addRule("description", "description", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
            })
            ->addRule("bgimage_xs", "bgimage_xs", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                if (!CheckerService::isValidUrl($value)) {
                    return __("Invalid url format");
                }
            })
            ->addRule("bgimage_sm", "bgimage_sm", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                if (!CheckerService::isValidUrl($value)) {
                    return __("Invalid url format");
                }
            })
            ->addRule("bgimage_md", "bgimage_md", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                if (!CheckerService::isValidUrl($value)) {
                    return __("Invalid url format");
                }
            })
            ->addRule("bgimage_lg", "bgimage_lg", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                if (!CheckerService::isValidUrl($value)) {
                    return __("Invalid url format");
                }
            })
            ->addRule("bgimage_xl", "bgimage_xl", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                if (!CheckerService::isValidUrl($value)) {
                    return __("Invalid url format");
                }
            })
            ->addRule("bgimage_xxl", "bgimage_xxl", function ($data) {
                if (!$value = $data["value"]) {
                    return false;
                }
                if (!CheckerService::isValidUrl($value)) {
                    return __("Invalid url format");
                }
            })
            ->addRule("max_confirmed", "max_confirmed", function ($data) {
                $ispublished = (int) $data["data"]["is_published"];
                if ($ispublished && !$data["value"]) {
                    return __("0 confirmed is not valid for publishing");
                }
            })
            ->addRule("returned", "returned", function ($data) {
                $ispublished = (int) $data["data"]["is_published"];
                $returned = (float) $data["data"]["returned"];
                if ($ispublished && ($returned < 1)) {
                    return __("Must be greater than 1 for publishing");
                }
            })
            ->addRule("date_from", "date_from", function ($data) {
                if (!$value = $data["value"]) {
                    return __("Empty field is not allowed");
                }
                if (!$this->datecomp->isValidDate($value)) {
                    return __("Invalid date {0}", $value);
                }
                if ($value > $data["data"]["date_to"]) {
                    return __("Date from is greater than Date to");
                }
                return false;
            })
            ->addRule("date_to", "date_to", function ($data) {
                if (!$value = $data["value"]) {
                    return __("Empty field is not allowed");
                }
                if (!$this->datecomp->isValidDate($value)) {
                    return __("Invalid date {0}", $value);
                }
                if ($value < $data["data"]["date_from"]) {
                    return __("Date to is lower than Date from");
                }
                return false;
            })
            ->addRule("date_execution", "date_execution", function ($data) {
                if (!$value = $this->datecomp->getDateInDbFormat($data["value"])) {
                    return __("Empty field is not allowed");
                }
                if (!$this->datecomp->isValidDate($value)) {
                    return __("Invalid date {0}", $value);
                }
                $dateto = $this->datecomp->addSecondsToDate($data["data"]["date_to"], 3600);
                if ($dateto > $value) {
                    return __("Value must be at least 1 hour after Date to");
                }
            })
            ->addRule("id_tz", "id_tz", function ($data) {
                if (!$value = $data["value"]) {
                    return __("Empty field is not allowed");
                }
                if (!RF::getInstanceOf(ArrayRepository::class)->getTimezoneDescriptionByIdPk($value)) {
                    return __("Invalid timezone");
                }
            })
        ;
        return $this->validator;
    }

    private function _map_entity(array &$promotion): void
    {
        unset(
            $promotion["slug"], $promotion["is_launched"], $promotion["num_viewed"],
            $promotion["num_subscribed"], $promotion["num_confirmed"], $promotion["num_executed"]
        );

        if (!$this->authService->hasAuthUserSystemProfile()) {
            unset($promotion["id_owner"]);
        }

        $promotion["slug"] = $this->textformat->getSlug($promotion["description"])."-".$promotion["id"];
        //paso a UTC
        $utc = CF::getInstanceOf(UtcComponent::class);
        $tzfrom = RF::getInstanceOf(ArrayRepository::class)->getTimezoneDescriptionByIdPk((int) $promotion["id_tz"]);
        $promotion["date_from"] = $utc->getSourceDtIntoTargetTz($promotion["date_from"], $tzfrom);
        $promotion["date_to"] = $utc->getSourceDtIntoTargetTz($promotion["date_to"], $tzfrom);
        $promotion["date_execution"] = $utc->getSourceDtIntoTargetTz($promotion["date_execution"], $tzfrom);

        if (
            $this->repopromotion->isPromotionLaunchedByPromotionUuid($promotion["uuid"])
            && $this->repopromotion->doesPromotionHaveSubscribersByPromotionUuid($promotion["uuid"])
        ) {
            unset(
                $promotion["id_owner"], $promotion["description"], $promotion["description"], $promotion["slug"],
                $promotion["id_tz"], $promotion["date_from"], $promotion["date_to"], $promotion["is_raffleable"],
                $promotion["is_cumulative"], $promotion["content"]
            );
        }
        if ($promotion["is_published"]) {
            $promotion["is_launched"] = 1;
        }
    }

    public function __invoke(): array
    {
        if (!$update = $this->_getRequestWithoutOperations($this->input)) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($errors = $this->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $update = $this->entitypromotion->getAllKeyValueFromRequest($update);
        $this->_checkEntityPermissionOrFail($update);
        $this->_map_entity($update);
        $this->entitypromotion->addSysUpdate($update, $this->authUserArray["id"]);

        $affected = $this->repopromotion->update($update);
        $promotion = $this->repopromotion->getEntityByEntityId($update["id"]);
        return [
            "affected" => $affected,
            "promotion" => [
                "id" => $promotion["id"],
                "uuid" => $promotion["uuid"],
                "is_launched" => $promotion["is_launched"],
                "slug" => $promotion["slug"],
                "is_published" => $promotion["is_published"],
                "num_viewed" => $promotion["num_viewed"],
                "num_subscribed" => $promotion["num_subscribed"],
                "num_confirmed" => $promotion["num_confirmed"],
                "num_executed" => $promotion["num_executed"],
                "disabled_date" => $promotion["disabled_date"],
            ]
        ];
    }
}
