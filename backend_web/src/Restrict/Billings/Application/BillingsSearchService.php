<?php
namespace App\Restrict\Billings\Application;

use App\Restrict\Billings\Domain\BillingsRepository;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class BillingsSearchService extends AppService
{
    private AuthService $auth;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();
        $this->input = $input;
    }

    private function _check_permission(): void
    {
        if(!$this->auth->is_user_allowed(UserPolicyType::BILLINGS_READ))
            $this->_exception(
                __("You are not allowed to perform this operation"),
                ExceptionType::CODE_FORBIDDEN
            );
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return RF::get(BillingsRepository::class)->set_auth($this->auth)->search($search);
    }

    public function get_datatable(): DatatableHelper
    {
        $dthelp = HF::get(DatatableHelper::class)->add_column("id")->is_visible(false);
        if ($this->auth->is_system())
            $dthelp
                ->add_column("e_owner")->add_label(__("Owner"))->add_tooltip(__("Owner"))
                ->add_column("e_business")->add_label(__("Business"))->add_tooltip(__("Business"));

        $dthelp
            ->add_column("uuid")->add_label(__("Code"))->add_tooltip(__("Code"))
            ->add_column("description")->add_label(__("Promotion"))->add_tooltip(__("Promotion"))
            ->add_column("num_executed")->add_label(__("Exec"))->add_tooltip(__("Exec"))
            ->add_column("e_returned")->add_label(__("Ret"))->add_tooltip(__("Ret"))
            ->add_column("e_earned")->add_label(__("Earn"))->add_tooltip(__("Earn"));

        if ($this->auth->is_system())
            $dthelp->add_column("e_percent")->add_label(__("%"))->add_tooltip(__("%"));

        $dthelp->add_column("e_commission")->add_label(__("Billed"))->add_tooltip(__("Billed"));

        if ($this->auth->is_system())
            $dthelp->add_column("e_b_earnings")->add_label(__("R. earn"))->add_tooltip(__("R. earn"));
        return $dthelp;
    }
}