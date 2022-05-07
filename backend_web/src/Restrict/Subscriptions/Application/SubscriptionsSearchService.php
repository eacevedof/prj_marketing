<?php
namespace App\Restrict\Subscriptions\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Promotions\Domain\PromotionRepository;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class SubscriptionsSearchService extends AppService
{
    private AuthService $auth;
    private PromotionRepository $repopromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        $this->repopromotion = RF::get(PromotionRepository::class);
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return $this->repopromotion->set_auth($this->auth)->search($search);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE)
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

        $dthelp->add_column("uuid")->add_label(__("Cod. Promo"))->add_tooltip(__("Cod. Promo"));
        if($this->auth->is_system())
            $dthelp->add_column("e_owner")->add_label(__("Owner"))->add_tooltip(__("Owner"));

        $dthelp->add_column("description")->add_label(__("Description"))->add_tooltip(__("Description"))
            //->add_column("slug")->add_label(__("Slug"))->add_tooltip(__("Slug"))
            //->add_column("content")->add_label(__("Content"))->add_tooltip(__("Content"))
            ->add_column("date_from")->add_label(__("Date from"))->add_tooltip(__("Date from"))
            ->add_column("date_to")->add_label(__("Date to"))->add_tooltip(__("Date to"))

            ->add_column("e_is_published")->add_label(__("Published"))->add_tooltip(__("Published"))
            ->add_column("invested")->add_label(__("Invested"))->add_tooltip(__("Invested"))
            ->add_column("returned")->add_label(__("Inv returned"))->add_tooltip(__("Inv returned"))
            //->add_column("notes")->add_label(__("Notes"))->add_tooltip(__("Notes"))
        ;

        if($this->auth->is_root())
            $dthelp->add_action("show")
                ->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("undel")
            ;

        if($this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("show")
            ;

        if($this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}