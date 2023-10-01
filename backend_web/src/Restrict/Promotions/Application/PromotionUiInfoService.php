<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Promotions\Domain\PromotionUiRepository;
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionUiInfoService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;
    private PromotionUiRepository $repopromoui;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        if (!$this->input = $input[0] ?? "") {
            $this->_throwException(__("No {0} code provided", "promotion_ui"), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->repopromoui = RF::getInstanceOf(PromotionUiRepository::class);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTION_UIS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTION_UIS_WRITE)
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

        $idauthuser = (int) $this->authUserArray["id"];
        $identowner = (int) $entity["id_owner"];
        //si el owner logado es propietario de la entidad
        if ($this->authService->isAuthUserBusinessOwner() && $idauthuser === $identowner) {
            return;
        }

        $idauthowner = $this->authService->getIdOwner();
        if ($this->authService->hasAuthUserBusinessManagerProfile() && $idauthowner === $identowner) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        if (!$promotion_ui = $this->repopromoui->getPromotionUiInfoByPromotionUiUuid($this->input)) {
            $this->_throwException(
                __("{0} with code {1} not found", __("Promotion UI"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        $this->_checkEntityPermissionOrFail($promotion_ui);
        return [
            "promotion_ui" => $promotion_ui
        ];
    }

    public function get_for_edit(): array
    {
        if (!$promotion_ui = $this->repopromoui->getPromotionUiInfoByPromotionUiUuid($this->input)) {
            $this->_throwException(
                __("{0} with code {1} not found", __("Promotion UI"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }
        $this->_checkEntityPermissionOrFail($promotion_ui);
        return $promotion_ui;
    }
}
