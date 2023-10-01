<?php

namespace App\Restrict\Promotions\Application;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Repositories\App\ArrayRepository;
use App\Shared\Domain\Enums\{ExceptionType, TimezoneType};
use App\Shared\Infrastructure\Components\Date\UtcComponent;
use App\Restrict\Promotions\Domain\{PromotionRepository, PromotionUiRepository};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionsInfoService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;

    private PromotionRepository $promotionRepository;
    private PromotionUiRepository $promotionUiRepository;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        if (!$this->input = $input[0] ?? "") {
            $this->_throwException(__("No {0} code provided", __("promotion")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->promotionRepository = RF::getInstanceOf(PromotionRepository::class);
        $this->promotionUiRepository = RF::getInstanceOf(PromotionUiRepository::class);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)
        )) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(array $entity): void
    {
        if ($this->authService->isAuthUserRoot() || $this->authService->isAuthUserSysadmin()) {
            return;
        }

        $idAuthUser = (int) $this->authUserArray["id"];
        $idEntityOwner = (int) $entity["id_owner"];
        //si el owner logado es propietario de la entidad
        if ($this->authService->isAuthUserBusinessOwner() && $idAuthUser === $idEntityOwner) {
            return;
        }

        $idAuthOwner = $this->authService->getIdOwner();
        if ($this->authService->hasAuthUserBusinessManagerProfile() && $idAuthOwner === $idEntityOwner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        if (!$promotion = $this->promotionRepository->getPromotionInfoByPromotionUuid($this->input)) {
            $this->_throwException(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        $this->_checkEntityPermissionOrFail($promotion);
        $this->_preparePromotionEntity($promotion);
        return [
            "promotion" => $promotion
        ];
    }

    private function _preparePromotionEntity(array &$promotion): void
    {
        $promotion["is_editable"] = !$this->promotionRepository->doesPromotionHaveSubscribersByPromotionUuid($promotion["uuid"]);
        $tzto = RF::getInstanceOf(ArrayRepository::class)->getTimezoneDescriptionByIdPk((int) $promotion["id_tz"]);
        if ($tzto === TimezoneType::UTC) {
            return;
        }
        $utc = CF::getInstanceOf(UtcComponent::class);
        $promotion["date_from"] = $utc->getSourceDtIntoTargetTz($promotion["date_from"], TimezoneType::UTC, $tzto);
        $promotion["date_to"] = $utc->getSourceDtIntoTargetTz($promotion["date_to"], TimezoneType::UTC, $tzto);
        $promotion["date_execution"] = $utc->getSourceDtIntoTargetTz($promotion["date_execution"], TimezoneType::UTC, $tzto);
    }

    public function get_for_edit(): array
    {
        $promotion = $this->promotionRepository->getPromotionInfoByPromotionUuid($this->input);
        if (!$promotion) {
            $this->_throwException(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }
        $this->_checkEntityPermissionOrFail($promotion);
        $this->_preparePromotionEntity($promotion);

        $hasPromotionUiPermission = $this->authService->getModulePermissions(
            UserPolicyType::MODULE_PROMOTIONS_UI,
            UserPolicyType::WRITE
        )[0];

        return [
            "promotion" => $promotion,
            "promotionui" => $hasPromotionUiPermission
                ? $this->_getRowWithSysDataByTz(
                    $this->promotionUiRepository->getPromotionUiByIdPromotion((int) $promotion["id"]),
                    $this->authService->getAuthUserTZ(),
                )
                : null,
        ];
    }
}
