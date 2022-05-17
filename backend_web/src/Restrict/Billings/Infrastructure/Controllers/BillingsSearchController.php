<?php
namespace App\Restrict\Billings\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Billings\Application\BillingsSearchService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use \Exception;

final class BillingsSearchController extends RestrictController
{

    public function index(?string $page=null): void
    {
        $this->_if_noauth_tologin();
        try {
            $search = SF::get(BillingsSearchService::class);

            $this->add_var(PageType::TITLE, __("Billings"))
                ->add_var(PageType::H1, __("Billings"))
                ->add_var("dthelp", $search->get_datatable())
                ->add_var("idowner", $this->auth->get_idowner())
                ->add_var("authread", $this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_READ))
                ->add_var("authwrite", $this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE))
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->response->location(UrlType::ERROR_FORBIDDEN);
        }
        catch (Exception $e) {
            $this->logerr($e->getMessage(), "promotionscontroller.index");
            $this->response->location(UrlType::ERROR_INTERNAL);
        }

    }//index

    //@get
    public function search(): void
    {
        if (!$this->auth->get_user())
            $this->_get_json()
                ->set_code(ResponseType::UNAUTHORIZED)
                ->set_error([__("Your session has finished please re-login")])
                ->show();

        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $search = SF::get_callable(BillingsSearchService::class, $this->request->get_get());
            $result = $search();
            $this->_get_json()->set_payload([
                "message"  => __("auth ok"),
                "result"   => $result["result"],
                "filtered" => $result["total"],
                "total"    => $result["total"],
            ])->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }//search

}//BillingsSearchController