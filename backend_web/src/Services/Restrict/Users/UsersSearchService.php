<?php
namespace App\Services\Restrict\Users;
use App\Components\Auth\AuthComponent;
use App\Factories\ComponentFactory as CF;
use App\Helpers\Views\DatatableHelper;
use App\Repositories\Base\UserPermissionsRepository;
use App\Services\AppService;
use App\Repositories\Base\UserRepository;
use App\Traits\SessionTrait;
use App\Traits\CookieTrait;
use App\Factories\RepositoryFactory as RF;
use App\Factories\HelperFactory as HF;

final class UsersSearchService extends AppService
{
    use SessionTrait;
    use CookieTrait;

    private string $domain;
    private array $input;
    private UserRepository $repository;
    private UserPermissionsRepository $permissionrepo;
    private AuthComponent $auth;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->_sessioninit();
        $this->repository = RF::get("Base/User");
        $this->permissionrepo = RF::get("Base/UserPermissions");
        $this->auth = CF::get("Auth/Auth");
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        $rows = $this->repository->search($search);
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

        if($this->auth->is_root() || $this->auth->is_sysadmin())
            $dthelp->add_action("edit")
                ->add_action("show")
                ->add_action("del");


        return $dthelp;
    }
}