<?php
namespace App\Services\Restrict\Users;

use App\Services\AppService;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\HelperFactory as HF;
use App\Factories\ComponentFactory as CF;
use App\Services\Auth\AuthService;
use App\Repositories\Base\UserPermissionsRepository;
use App\Repositories\Base\UserRepository;
use App\Helpers\Views\DatatableHelper;
use App\Enums\PolicyType;

final class UsersSearchService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $domain;
    private AuthService $auth;
    private UserRepository $repouser;
    private UserPermissionsRepository $repopermission;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->auth = SF::get_auth();
        $this->repouser = RF::get("Base/User");
        $this->repopermission = RF::get("Base/UserPermissions");
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        $rows = $this->repouser->set_auth($this->auth)->search($search);
        return $rows;
    }

    public function get_datatable(): DatatableHelper
    {
        $dthelp = HF::get("Views/Datatable")->add_column("id")->is_visible(false);

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

        if($this->auth->is_user_allowed(PolicyType::USERS_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del");

        if($this->auth->is_user_allowed(PolicyType::USERS_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}