<?php
namespace App\Restrict\Subscriptions\Application;

use App\Restrict\Subscriptions\Domain\PromotionCapSubscriptionsRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class SubscriptionsSearchService extends AppService
{
    private AuthService $auth;
    private PromotionCapSubscriptionsRepository $repopromotion;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        $this->repopromotion = RF::get(PromotionCapSubscriptionsRepository::class);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE)
        ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return $this->repopromotion->set_auth($this->auth)->search($search);
    }

    public function get_datatable(): DatatableHelper
    {
        $dthelp = HF::get(DatatableHelper::class)->add_column("id")->is_visible(false);

        if($this->auth->is_root())
            $dthelp
                ->add_column("delete_date")->add_label(__("Deleted at"))
                ->add_column("e_deletedby")->add_label(__("Deleted by"));

        $dthelp->add_column("uuid")->add_label(__("Cod. Susbscription"))->add_tooltip(__("Cod. Susbscription"));
        if($this->auth->is_system())
            $dthelp->add_column("e_owner")->add_label(__("Owner"))->add_tooltip(__("Owner"));

        if($this->auth->is_system())
            $dthelp->add_column("e_business")->add_label(__("Business"))->add_tooltip(__("Business"));

        $dthelp
            ->add_column("e_promotion")->add_label(__("Promotion"))->add_tooltip(__("Promotion"))
            ->add_column("e_usercode")->add_label(__("Cod. User"))->add_tooltip(__("Cod. User"))
            ->add_column("e_username")->add_label(__("Name"))->add_tooltip(__("Name"))
            ->add_column("e_status")->add_label(__("Status"))->add_tooltip(__("Status"))
            ->add_column("notes")->add_label(__("Notes"))->add_tooltip(__("Notes"))
        ;

        if($this->auth->is_root())
            $dthelp->add_action("show");

        if($this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_READ))
            $dthelp->add_action("show");

        if ($this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE))
            $dthelp->add_action("edit");

        return $dthelp;
    }
}