<?php
namespace App\Services\Restrict\Xxxs;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\HelperFactory as HF;
use App\Factories\ComponentFactory as CF;
use App\Services\Auth\AuthService;
use App\Repositories\App\XxxRepository;
use App\Helpers\Views\DatatableHelper;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;

final class XxxsSearchService extends AppService
{
    private AuthService $auth;
    private XxxRepository $repoxxx;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        $this->repoxxx = RF::get("App/Xxx");
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return $this->repoxxx->set_auth($this->auth)->search($search);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(PolicyType::XXXS_READ)
            || $this->auth->is_user_allowed(PolicyType::XXXS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function get_datatable(): DatatableHelper
    {
        $dthelp = HF::get("Views/Datatable")->add_column("id")->is_visible(false);

        if($this->auth->is_root())
            $dthelp
                ->add_column("delete_date")->add_label(__("Deleted at"))
                ->add_column("e_deletedby")->add_label(__("Deleted by"));

        $dthelp->add_column("uuid")->add_label(__("Code"))->add_tooltip(__("uuid"))
            %DT_COLUMNS%
        ;

        if($this->auth->is_root())
            $dthelp->add_action("show")
                ->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("undel")
            ;

        if($this->auth->is_user_allowed(PolicyType::XXXS_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del");

        if($this->auth->is_user_allowed(PolicyType::XXXS_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}