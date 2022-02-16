<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\BusinessData\Infrastructure\Controllers\BusinessDataSearchController
 * @file BusinessDataSearchController.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\BusinessData\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\BusinessData\Application\BusinessDataSearchService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use \Exception;

final class BusinessDataSearchController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get(PicklistService::class);
    }

    public function index(?string $page=null): void
    {
        try {
            $search = SF::get(BusinessDataSearchService::class);

            $this->add_var(PageType::TITLE, __("Business data"))
                ->add_var(PageType::H1, __("Business data"))
                ->add_var("dthelp", $search->get_datatable())
                ->add_var("idowner", $this->auth->get_idowner())
                ->add_var("authread", $this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_READ))
                ->add_var("authwrite", $this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE))
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->response->location(UrlType::ERROR_FORBIDDEN);
        }
        catch (Exception $e) {
            $this->logerr($e->getMessage(), "business_datascontroller.index");
            $this->response->location(UrlType::ERROR_INTERNAL);
        }

    }//index

    //@get
    public function search(): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $search = SF::get_callable(BusinessDataSearchService::class, $this->request->get_get());
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

}//BusinessDataSearchController
