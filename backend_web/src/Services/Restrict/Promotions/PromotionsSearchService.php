<?php
namespace App\Services\Restrict\Promotions;

use App\Services\AppService;
use App\Factories\ServiceFactory as SF;
use App\Factories\RepositoryFactory as RF;
use App\Factories\HelperFactory as HF;
use App\Factories\ComponentFactory as CF;
use App\Services\Auth\AuthService;
use App\Repositories\App\PromotionRepository;
use App\Helpers\Views\DatatableHelper;
use App\Enums\PolicyType;
use App\Enums\ExceptionType;

final class PromotionsSearchService extends AppService
{
    private AuthService $auth;
    private PromotionRepository $repopromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        $this->repopromotion = RF::get("App/Promotion");
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return $this->repopromotion->set_auth($this->auth)->search($search);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(PolicyType::PROMOTIONS_READ)
            || $this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE)
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

        $dthelp
            ->add_column("uuid")->add_label(__("Cod. Promo"))->add_tooltip(__("Cod. Promo"))
            ->add_column("id_owner")->add_label(__("Owner"))->add_tooltip(__("Owner"))
            ->add_column("description")->add_label(__("Description"))->add_tooltip(__("Description"))
            //->add_column("slug")->add_label(__("Slug"))->add_tooltip(__("Slug"))
            //->add_column("content")->add_label(__("Content"))->add_tooltip(__("Content"))
            ->add_column("id_type")->add_label(__("Type"))->add_tooltip(__("Type"))
            ->add_column("date_from")->add_label(__("Date from"))->add_tooltip(__("Date from"))
            ->add_column("date_to")->add_label(__("Date to"))->add_tooltip(__("Date to"))
            ->add_column("url_social")->is_visible(false)->add_label(__("Url social"))->add_tooltip(__("Url social"))
            ->add_column("url_design")->is_visible(false)->add_label(__("Url design"))->add_tooltip(__("Url design"))
            ->add_column("is_active")->add_label(__("Enabled"))->add_tooltip(__("Enabled"))
            ->add_column("invested")->add_label(__("Invested"))->add_tooltip(__("Invested"))
            ->add_column("returned")->add_label(__("Inv returned"))->add_tooltip(__("Inv returned"))
            ->add_column("notes")->add_label(__("Notes"))->add_tooltip(__("Notes"))
        ;

        if($this->auth->is_root())
            $dthelp->add_action("show")
                ->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("undel")
            ;

        if($this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del");

        if($this->auth->is_user_allowed(PolicyType::PROMOTIONS_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}