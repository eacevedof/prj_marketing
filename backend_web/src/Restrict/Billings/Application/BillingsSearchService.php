<?php

namespace App\Restrict\Billings\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Billings\Domain\BillingsRepository;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Shared\Infrastructure\Factories\{
    ComponentFactory as CF,
    HelperFactory as HF,
    RepositoryFactory as RF,
    ServiceFactory as SF
};

final class BillingsSearchService extends AppService
{
    private AuthService $authService;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();
        $this->input = $input;
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::BILLINGS_READ)) {
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
        }
    }

    public function __invoke(): array
    {
        $search = CF::getDatatableComponent($this->input)->getSearchPayload();
        return RF::getInstanceOf(BillingsRepository::class)->setAuthService($this->authService)->search($search);
    }

    public function getDatatableHelper(): DatatableHelper
    {
        $dtHelper = HF::get(DatatableHelper::class)->addColumn("id")->isVisible(false);
        if ($this->authService->hasAuthUserSystemProfile()) {
            $dtHelper
                ->addColumn("e_owner")->addLabel(__("Owner"))->addTooltip(__("Owner"))
                ->addColumn("e_business")->addLabel(__("Business"))->addTooltip(__("Business"));
        }

        $dtHelper
            ->addColumn("uuid")->addLabel(__("Code"))->addTooltip(__("Code"))
            ->addColumn("description")->addLabel(__("Promotion"))->addTooltip(__("Promotion"))
            ->addColumn("num_executed")->addLabel(__("Exec"))->addTooltip(__("Exec"))
            ->addColumn("e_returned")->addLabel(__("Ret"))->addTooltip(__("Ret"))
            ->addColumn("e_earned")->addLabel(__("Earn"))->addTooltip(__("Earn"));

        $dtHelper->addAction("export");
        if ($this->authService->hasAuthUserSystemProfile()) {
            $dtHelper->addColumn("e_percent")->addLabel(__("%"))->addTooltip(__("%"));
        }

        $dtHelper->addColumn("e_commission")->addLabel(__("Bill"))->addTooltip(__("Bill"));
        $dtHelper->addColumn("e_invested")->addLabel(__("Inv"))->addTooltip(__("Inv"));

        if ($this->authService->hasAuthUserSystemProfile()) {
            $dtHelper->addColumn("e_b_earnings")->addLabel(__("R. earn"))->addTooltip(__("R. earn"));
        }
        return $dtHelper;
    }
}
