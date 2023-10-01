<?php

namespace App\Restrict\Promotions\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, HelperFactory as HF, RepositoryFactory as RF, ServiceFactory as SF};

final class PromotionsSearchService extends AppService
{
    private AuthService $authService;
    private PromotionRepository $promotionRepository;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        $this->promotionRepository = RF::getInstanceOf(PromotionRepository::class);
    }

    public function __invoke(): array
    {
        $search = CF::getDatatableComponent($this->input)->getSearchPayload();
        return $this->promotionRepository->setAuthService($this->authService)->search($search);
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

    public function getDatatableHelper(): DatatableHelper
    {
        $datatableHelper = HF::get(DatatableHelper::class)->addColumn("id")->isVisible(false);

        if ($this->authService->isAuthUserRoot()) {
            $datatableHelper
                ->addColumn("delete_date")->addLabel(__("Deleted at"))
                ->addColumn("e_deletedby")->addLabel(__("Deleted by"));
        }

        $datatableHelper->addColumn("uuid")->addLabel(__("Cod. Promo"))->addTooltip(__("Cod. Promo"));
        if ($this->authService->hasAuthUserSystemProfile()) {
            $datatableHelper->addColumn("e_owner")->addLabel(__("Owner"))->addTooltip(__("Owner"));
        }

        $datatableHelper->addColumn("description")->addLabel(__("Description"))->addTooltip(__("Description"))
            //->add_column("slug")->add_label(__("Slug"))->add_tooltip(__("Slug"))
            //->add_column("content")->add_label(__("Terms and conditions"))->add_tooltip(__("Terms and conditions"))
            ->addColumn("date_from")->addLabel(__("Date from"))->addTooltip(__("Date from"))
            ->addColumn("date_to")->addLabel(__("Date to"))->addTooltip(__("Date to"))

            ->addColumn("e_is_published")->addLabel(__("Published"))->addTooltip(__("Published"))
            ->addColumn("num_confirmed")->addLabel(__("Conf"))->addTooltip(__("Conf"))
            ->addColumn("num_executed")->addLabel(__("Exec"))->addTooltip(__("Exec"))
            ->addColumn("invested")->addLabel(__("Invested"))->addTooltip(__("Invested"))
            ->addColumn("returned")->addLabel(__("Inv returned"))->addTooltip(__("Inv returned"))
            //->add_column("notes")->add_label(__("Notes"))->add_tooltip(__("Notes"))
        ;

        if ($this->authService->isAuthUserRoot()) {
            $datatableHelper
                ->addColumn("disabled_date")->addLabel(__("Disabled date"))->addTooltip(__("Disabled date"))
                ->addAction("show")
                ->addAction("export")
                ->addAction("add")
                ->addAction("edit")
                ->addAction("del")
                ->addAction("undel")
            ;
        }

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)) {
            $datatableHelper->addAction("add")
                ->addAction("edit")
                ->addAction("del")
                ->addAction("show")
                ->addAction("export")
            ;
        }

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_READ)) {
            $datatableHelper->addAction("show")
                ->addAction("export")
            ;
        }

        return $datatableHelper;
    }
}
