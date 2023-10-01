<?php

namespace App\Restrict\Subscriptions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Infrastructure\Factories\{
    ComponentFactory as CF,
    HelperFactory as HF,
    RepositoryFactory as RF,
    ServiceFactory as SF
};

final class SubscriptionsSearchService extends AppService
{
    private AuthService $authService;
    private PromotionCapSubscriptionsRepository $promotionCapSubscriptionsRepository;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
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

    public function __invoke(): array
    {
        $search = CF::getDatatableComponent($this->input)->getSearchPayload();
        return $this->promotionCapSubscriptionsRepository->setAuthService($this->authService)->search($search);
    }

    public function getDatatableHelper(): DatatableHelper
    {
        $datatableHelper = HF::get(DatatableHelper::class)->addColumn("id")->isVisible(false);

        if ($this->authService->isAuthUserRoot()) {
            $datatableHelper
                ->addColumn("delete_date")->addLabel(__("Deleted at"))
                ->addColumn("e_deletedby")->addLabel(__("Deleted by"));
        }

        $datatableHelper->addColumn("uuid")->addLabel(__("Cod. Susbscription"))->addTooltip(__("Cod. Susbscription"));
        if ($this->authService->hasAuthUserSystemProfile()) {
            $datatableHelper->addColumn("e_owner")->addLabel(__("Owner"))->addTooltip(__("Owner"));
        }

        if ($this->authService->hasAuthUserSystemProfile()) {
            $datatableHelper->addColumn("e_business")->addLabel(__("Business"))->addTooltip(__("Business"));
        }

        $datatableHelper
            ->addColumn("c_is_test")->addLabel(__("Test"))->addTooltip(__("Test"))
            ->addColumn("e_promotion")->addLabel(__("Promotion"))->addTooltip(__("Promotion"))
            ->addColumn("e_usercode")->addLabel(__("Cod. User"))->addTooltip(__("Cod. User"))
            ->addColumn("e_username")->addLabel(__("Name"))->addTooltip(__("Name"))
            ->addColumn("e_status")->addLabel(__("Status"))->addTooltip(__("Status"))
            ->addColumn("notes")->addLabel(__("Notes"))->addTooltip(__("Notes"))
        ;

        $datatableHelper->addAction("export");
        if ($this->authService->isAuthUserRoot()) {
            $datatableHelper->addAction("show");
        }

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_READ)) {
            $datatableHelper->addAction("show");
        }

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_WRITE)) {
            $datatableHelper->addAction("edit");
        }

        return $datatableHelper;
    }
}
