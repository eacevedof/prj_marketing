<?php

namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\{UserPermissionsRepository, UserRepository};
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, HelperFactory as HF, RepositoryFactory as RF, ServiceFactory as SF};

final class UsersSearchService extends AppService
{
    private AuthService $authService;
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;

    public function __construct(array $input)
    {
        $this->authService = SF::getAuthService();
        $this->_checkPermissionOrFail();

        $this->input = $input;
        $this->repouser = RF::getInstanceOf(UserRepository::class);
        $this->repopermission = RF::getInstanceOf(UserPermissionsRepository::class);
    }

    public function __invoke(): array
    {
        $search = CF::getDatatableComponent($this->input)->getSearchPayload();
        return $this->repouser->setAuthService($this->authService)->search($search);
    }

    private function _checkPermissionOrFail(): void
    {
        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if ($this->authService->isAuthUserSuperRoot()) {
            return;
        }

        if (!(
            $this->authService->hasAuthUserPolicy(UserPolicyType::USERS_READ)
            || $this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE)
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

        $datatableHelper->addColumn("uuid")->addLabel(__("Code"))->addTooltip(__("uuid"))
            ->addColumn("fullname")->addLabel(__("Fullname"))
            ->addColumn("email")->addLabel(__("Email"))
            ->addColumn("phone")->addLabel(__("Phone"))
            ->addColumn("e_parent")->addLabel(__("Superior"))
            ->addColumn("e_profile")->addLabel(__("Profile"))
            ->addColumn("e_country")->addLabel(__("Country"))
            ->addColumn("e_language")->addLabel(__("Language"));

        if ($this->authService->isAuthUserRoot()) {
            $datatableHelper->addAction("show")
                ->addAction("add")
                ->addAction("edit")
                ->addAction("del")
                ->addAction("undel")
                ->addAction("export")
            ;
        }

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE)) {
            $datatableHelper->addAction("add")
                ->addAction("edit")
                ->addAction("del")
                ->addAction("export")
            ;
        }

        if ($this->authService->hasAuthUserPolicy(UserPolicyType::USERS_READ)) {
            $datatableHelper->addAction("show")
                ->addAction("export")
            ;
        }

        return $datatableHelper;
    }
}
