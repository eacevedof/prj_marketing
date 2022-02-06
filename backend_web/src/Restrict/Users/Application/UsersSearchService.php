<?php
namespace App\Restrict\Users\Application;

use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Users\Domain\UserPermissionsRepository;
use App\Restrict\Users\Domain\UserRepository;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\Enums\UserPolicyType;

final class UsersSearchService extends AppService
{
    private AuthService $auth;
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        $this->repouser = RF::get(UserRepository::class);
        $this->repopermission = RF::get(UserPermissionsRepository::class);
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return $this->repouser->set_auth($this->auth)->search($search);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::USERS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::USERS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function get_datatable(): DatatableHelper
    {
        $dthelp = HF::get(DatatableHelper::class)->add_column("id")->is_visible(false);

        if($this->auth->is_root())
            $dthelp
                ->add_column("delete_date")->add_label(__("Deleted at"))
                ->add_column("e_deletedby")->add_label(__("Deleted by"));

        $dthelp->add_column("uuid")->add_label(__("Code"))->add_tooltip(__("uuid"))
            ->add_column("fullname")->add_label(__("Fullname"))
            ->add_column("email")->add_label(__("Email"))
            ->add_column("phone")->add_label(__("Phone"))
            ->add_column("e_parent")->add_label(__("Superior"))
            ->add_column("e_profile")->add_label(__("Profile"))
            ->add_column("e_country")->add_label(__("Country"))
            ->add_column("e_language")->add_label(__("Language"));

        if($this->auth->is_root())
            $dthelp->add_action("show")
                ->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("undel")
            ;

        if($this->auth->is_user_allowed(UserPolicyType::USERS_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del");

        if($this->auth->is_user_allowed(UserPolicyType::USERS_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}