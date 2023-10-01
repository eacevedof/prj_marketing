<?php

namespace App\Restrict\Subscriptions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Infrastructure\Factories\{RepositoryFactory as RF, ServiceFactory as SF};

final class SubscriptionsInfoService extends AppService
{
    private AuthService $authService;
    private array $authUserArray;

    private PromotionCapSubscriptionsRepository $promotionCapSubscriptionsRepository;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        if (!$this->input = $input[0] ?? "") {
            $this->_throwException(__("No {0} code provided", __("subscription")), ExceptionType::CODE_BAD_REQUEST);
        }

        $this->authUserArray = $this->authService->getAuthUserArray();
        $this->promotionCapSubscriptionsRepository = RF::getInstanceOf(PromotionCapSubscriptionsRepository::class);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_WRITE)
        )) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    private function _checkEntityPermissionOrFail(array $entitycapsubs): void
    {
        if ($this->authService->isAuthUserRoot() || $this->authService->isAuthUserSysadmin()) {
            return;
        }

        $idAuthUser = (int) $this->authUser["id"];
        $idEntityOwner = (int) $entitycapsubs["id_owner"];
        //si el owner logado es propietario de la entidad
        if ($this->authService->isAuthUserBusinessOwner() && ($idAuthUser === $idEntityOwner)) {
            return;
        }

        $idauthowner = $this->authService->getIdOwner();
        if ($this->authService->hasAuthUserBusinessManagerProfile() && ($idauthowner === $idEntityOwner)) {
            return;
        }

        $this->_throwException(
            __("You are not allowed to perform this operation"),
            ExceptionType::CODE_FORBIDDEN
        );
    }

    public function __invoke(): array
    {
        if (!$capSubscription = $this->promotionCapSubscriptionsRepository->getCapSubscriptionInfoBySubscriptionUuid($this->input)) {
            $this->_throwException(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }

        $this->_checkEntityPermissionOrFail($capSubscription);
        return [
            "subscription" => $capSubscription
        ];
    }

    public function get_info_for_execute_date(): array
    {
        $capsubscription = $this->promotionCapSubscriptionsRepository->getCapSubscriptionInfoForExecuteDate($this->input);
        if (!$capsubscription) {
            $this->_throwException(
                __("{0} with code {1} not found", __("Promotion"), $this->input),
                ExceptionType::CODE_NOT_FOUND
            );
        }
        $this->_checkEntityPermissionOrFail($capsubscription);

        return [
            "subscription" => $capsubscription,
        ];
    }
}
