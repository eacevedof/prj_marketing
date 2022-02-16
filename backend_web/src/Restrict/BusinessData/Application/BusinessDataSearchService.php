<?php
namespace App\Restrict\BusinessData\Application;

use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\HelperFactory as HF;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\BusinessData\Domain\BusinessDataRepository;
use App\Shared\Infrastructure\Helpers\Views\DatatableHelper;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\ExceptionType;

final class BusinessDataSearchService extends AppService
{
    private AuthService $auth;
    private BusinessDataRepository $repobusinessdata;

    public function __construct(array $input)
    {
        $this->auth = SF::get_auth();
        $this->_check_permission();

        $this->input = $input;
        $this->repobusinessdata = RF::get(BusinessDataRepository::class);
    }

    public function __invoke(): array
    {
        $search = CF::get_datatable($this->input)->get_search();
        return $this->repobusinessdata->set_auth($this->auth)->search($search);
    }

    private function _check_permission(): void
    {
        if(!(
            $this->auth->is_user_allowed(UserPolicyType::BUSINESS_DATAS_READ)
            || $this->auth->is_user_allowed(UserPolicyType::BUSINESS_DATAS_WRITE)
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
            ->add_column("id")->add_label(__("tr_id"))->add_tooltip(__("tr_id"))
->add_column("uuid")->add_label(__("tr_uuid"))->add_tooltip(__("tr_uuid"))
->add_column("id_user")->add_label(__("tr_id_user"))->add_tooltip(__("tr_id_user"))
->add_column("slug")->add_label(__("tr_slug"))->add_tooltip(__("tr_slug"))
->add_column("user_logo_1")->add_label(__("tr_user_logo_1"))->add_tooltip(__("tr_user_logo_1"))
->add_column("user_logo_2")->add_label(__("tr_user_logo_2"))->add_tooltip(__("tr_user_logo_2"))
->add_column("user_logo_3")->add_label(__("tr_user_logo_3"))->add_tooltip(__("tr_user_logo_3"))
->add_column("url_favicon")->add_label(__("tr_url_favicon"))->add_tooltip(__("tr_url_favicon"))
->add_column("head_bgcolor")->add_label(__("tr_head_bgcolor"))->add_tooltip(__("tr_head_bgcolor"))
->add_column("head_color")->add_label(__("tr_head_color"))->add_tooltip(__("tr_head_color"))
->add_column("head_bgimage")->add_label(__("tr_head_bgimage"))->add_tooltip(__("tr_head_bgimage"))
->add_column("body_bgcolor")->add_label(__("tr_body_bgcolor"))->add_tooltip(__("tr_body_bgcolor"))
->add_column("body_color")->add_label(__("tr_body_color"))->add_tooltip(__("tr_body_color"))
->add_column("body_bgimage")->add_label(__("tr_body_bgimage"))->add_tooltip(__("tr_body_bgimage"))
->add_column("site")->add_label(__("tr_site"))->add_tooltip(__("tr_site"))
->add_column("url_social_fb")->add_label(__("tr_url_social_fb"))->add_tooltip(__("tr_url_social_fb"))
->add_column("url_social_ig")->add_label(__("tr_url_social_ig"))->add_tooltip(__("tr_url_social_ig"))
->add_column("url_social_twitter")->add_label(__("tr_url_social_twitter"))->add_tooltip(__("tr_url_social_twitter"))
->add_column("url_social_tiktok")->add_label(__("tr_url_social_tiktok"))->add_tooltip(__("tr_url_social_tiktok"))
        ;

        if($this->auth->is_root())
            $dthelp->add_action("show")
                ->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("undel")
            ;

        if($this->auth->is_user_allowed(UserPolicyType::BUSINESS_DATAS_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del");

        if($this->auth->is_user_allowed(UserPolicyType::BUSINESS_DATAS_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}