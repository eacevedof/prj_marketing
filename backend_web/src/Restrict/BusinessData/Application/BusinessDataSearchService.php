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
            $this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_READ)
            || $this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE)
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
            ->add_column("id")->add_label(__("Nº"))->add_tooltip(__("Nº"))
->add_column("uuid")->add_label(__("uuid"))->add_tooltip(__("uuid"))
->add_column("id_user")->add_label(__("User"))->add_tooltip(__("User"))
->add_column("slug")->add_label(__("Slug"))->add_tooltip(__("Slug"))
->add_column("user_logo_1")->add_label(__("Url logo sm"))->add_tooltip(__("Url logo sm"))
->add_column("user_logo_2")->add_label(__("Url logo md"))->add_tooltip(__("Url logo md"))
->add_column("user_logo_3")->add_label(__("Url logo lg"))->add_tooltip(__("Url logo lg"))
->add_column("url_favicon")->add_label(__("Url favicon"))->add_tooltip(__("Url favicon"))
->add_column("head_bgcolor")->add_label(__("Head bg color"))->add_tooltip(__("Head bg color"))
->add_column("head_color")->add_label(__("Head color"))->add_tooltip(__("Head color"))
->add_column("head_bgimage")->add_label(__("Head bg image"))->add_tooltip(__("Head bg image"))
->add_column("body_bgcolor")->add_label(__("Body bg color"))->add_tooltip(__("Body bg color"))
->add_column("body_color")->add_label(__("Body color"))->add_tooltip(__("Body color"))
->add_column("body_bgimage")->add_label(__("Url body bg image"))->add_tooltip(__("Url body bg image"))
->add_column("site")->add_label(__("Url site"))->add_tooltip(__("Url site"))
->add_column("url_social_fb")->add_label(__("Url Facebook"))->add_tooltip(__("Url Facebook"))
->add_column("url_social_ig")->add_label(__("Url Instagram"))->add_tooltip(__("Url Instagram"))
->add_column("url_social_twitter")->add_label(__("Url Twitter"))->add_tooltip(__("Url Twitter"))
->add_column("url_social_tiktok")->add_label(__("Url TikTok"))->add_tooltip(__("Url TikTok"))
        ;

        if($this->auth->is_root())
            $dthelp->add_action("show")
                ->add_action("add")
                ->add_action("edit")
                ->add_action("del")
                ->add_action("undel")
            ;

        if($this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_WRITE))
            $dthelp->add_action("add")
                ->add_action("edit")
                ->add_action("del");

        if($this->auth->is_user_allowed(UserPolicyType::BUSINESSDATA_READ))
            $dthelp->add_action("show");

        return $dthelp;
    }
}