<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsSearchController
 * @file PromotionsSearchController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Promotions\Infrastructure\Controllers;

use App\Restrict\Promotions\Application\PromotionsSearchService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use \Exception;

final class PromotionsSearchController extends RestrictController
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
            $search = SF::get(PromotionsSearchService::class);

            $this->add_var(PageType::TITLE, __("Promotions"))
                ->add_var(PageType::H1, __("Promotions"))
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
            $this->logerr($e->getMessage(), "promotionscontroller.index");
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
            $search = SF::get_callable(PromotionsSearchService::class, $this->request->get_get());
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

}//PromotionsSearchController
