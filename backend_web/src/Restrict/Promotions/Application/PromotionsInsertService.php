<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Checker\Application\CheckerService;
use App\Shared\Infrastructure\Bus\EventBus;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Entities\FieldsValidator;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;
use App\Restrict\Promotions\Domain\Events\PromotionWasCreatedEvent;
use App\Shared\Infrastructure\Factories\Specific\ValidatorFactory as VF;
use App\Restrict\Promotions\Domain\{PromotionEntity, PromotionRepository};
use App\Shared\Infrastructure\Components\Date\{DateComponent, UtcComponent};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, EntityFactory as MF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionsInsertService extends AppService implements IEventDispatcher
{
    use RequestTrait;

    private AuthService $authService;
    private array $authUserArray;
    private PromotionRepository $repopromotion;
    private FieldsValidator $validator;
    private PromotionEntity $entitypromotion;
    private TextComponent $textformat;
    private DateComponent $datecomp;
    private ArrayRepository $repoapparray;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->datecomp = CF::getInstanceOf(DateComponent::class);
        $this->_map_dates($input);
        $this->input = $input;
        $this->entitypromotion = MF::getInstanceOf(PromotionEntity::class);
        $this->validator = VF::getFieldValidator($this->input, $this->entitypromotion);

        $this->repopromotion = RF::getInstanceOf(PromotionRepository::class)->setAppEntity($this->entitypromotion);
        $this->repoapparray = RF::getInstanceOf(ArrayRepository::class);
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
        $input["tags"] = $input["tags"] = CF::getInstanceOf(TextComponent::class)->getBlanksAsNull($input["tags"]);
    }

    private function _skip_validation(): self
    {
        $this->validator
            ->addSkipableField("is_published")
            ->addSkipableField("is_launched")
            ->addSkipableField("date_execution");
        return $this;
    }

    private function _add_rules(): FieldsValidator
    {
        $this->validator
            ->addRule("id_owner", "empty", function ($data) {
                if ($this->authService->hasAuthUserSystemProfile() && !trim($data["value"])) {
                    return __("Empty field is not allowed");
                }
                return false;
            })
            ->addRule("description", "empty", function ($data) {
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
            ->addRule("id_tz", "id_tz", function ($data) {
                return $data["value"] ? false : __("Empty field is not allowed");
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
            });

        return $this->validator;
    }

    private function _map_entity(array &$promotion): void
    {
        if (!$this->authService->hasAuthUserSystemProfile()) {
            $promotion["id_owner"] = $this->authService->getIdOwner();
        }
        $this->entitypromotion->addSysInsert($promotion, $this->authUserArray["id"]);
        $promotion["uuid"] = uniqid();
        unset(
            $promotion["slug"], $promotion["is_published"],$promotion["is_launched"],$promotion["slug"],$promotion["is_raffleable"],
            $promotion["is_cumulative"], $promotion["max_confirmed"], $promotion["invested"], $promotion["returned"], $promotion["date_execution"]
        );
        $promotion["slug"] = $this->textformat->getSlug($promotion["description"]);
        $utc = CF::getInstanceOf(UtcComponent::class);
        $tzfrom = RF::getInstanceOf(ArrayRepository::class)->getTimezoneDescriptionByIdPk((int) $promotion["id_tz"]);
        //paso fechas a utc
        $promotion["date_from"] = $utc->getSourceDtIntoTargetTz($promotion["date_from"], $tzfrom);
        $promotion["date_to"] = $dateto = $utc->getSourceDtIntoTargetTz($promotion["date_to"], $tzfrom);
        $promotion["date_execution"] = $this->datecomp->addSecondsToDate($dateto, 3600);
    }

    private function _dispatchEvents(array $payload): void
    {
        EventBus::instance()->publish(...[
            PromotionWasCreatedEvent::fromPrimitives($payload["promotion"]["id"], $payload["promotion"])
        ]);
    }

    public function __invoke(): array
    {
        if (!$promotion = $this->_getRequestWithoutOperations($this->input)) {
            $this->_throwException(__("Empty data"), ExceptionType::CODE_BAD_REQUEST);
        }

        if ($errors = $this->_skip_validation()->_add_rules()->getErrors()) {
            $this->_setErrors($errors);
            throw new FieldsException(__("Fields validation errors"));
        }

        $promotion = $this->entitypromotion->getAllKeyValueFromRequest($promotion);
        $this->_map_entity($promotion);
        $id = $this->repopromotion->insert($promotion);
        $this->repopromotion->updatePromotionSlugWithPromotionId($id);
        $promotion = $this->repopromotion->getEntityByEntityId($id);

        $this->_dispatchEvents(["promotion" => $promotion]);

        return [
            "id" => $id,
            "uuid" => $promotion["uuid"]
        ];
    }
}
