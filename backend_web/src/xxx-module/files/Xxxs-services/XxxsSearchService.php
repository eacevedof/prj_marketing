<?php
namespace App\Restrict\Xxxs\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Xxxs\Domain\XxxRepository;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class XxxsSearchService extends AppService
{
    private AuthService $authService;
    private XxxRepository $repoxxx;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        $this->repoxxx = RF::getInstanceOf(XxxRepository::class);
    }

    public function __invoke(): array
    {
        $search = CF::getDatatableComponent($this->input)->getSearchPayload();
        return $this->repoxxx->set_auth($this->authService)->search($search);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) return;

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_WRITE)
        ))
            $this->_throwException(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function getDatatableHelper(): DatatableHelper
    {
        $datatableHelper = HF::get(DatatableHelper::class)->addColumn("id")->isVisible(false);

        if ($this->authService->isAuthUserRoot())
            $datatableHelper
                ->addColumn("delete_date")->addLabel(__("Deleted at"))
                ->addColumn("e_deletedby")->addLabel(__("Deleted by"));

        $datatableHelper->addColumn("uuid")->addLabel(__("Code"))->addTooltip(__("uuid"))
            %DT_COLUMNS%
        ;

        if ($this->authService->isAuthUserRoot())
            $datatableHelper->addAction("show")
                ->addAction("add")
                ->addAction("edit")
                ->addAction("del")
                ->addAction("undel")
            ;

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_WRITE))
            $datatableHelper->addAction("add")
                ->addAction("edit")
                ->addAction("del");

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_READ))
            $datatableHelper->addAction("show");

        return $datatableHelper;
    }
}