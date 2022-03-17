<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersSearchController
 * @file UsersSearchController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Infrastructure\Controllers;

use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Application\UsersSearchService;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use \Exception;

final class UsersSearchController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->_if_noauth_tologin();
        $this->picklist = SF::get(PicklistService::class);
    }

    public function index(?string $page=null): void
    {
        try {
            $search = SF::get(UsersSearchService::class);

            $this->add_var(PageType::TITLE, __("Users"))
                ->add_var(PageType::H1, __("Users"))
                ->add_var("languages", $this->picklist->get_languages())
                ->add_var("profiles", $this->picklist->get_profiles())
                ->add_var("countries", $this->picklist->get_countries())
                ->add_var("dthelp", $search->get_datatable())
                ->add_var("authread", $this->auth->is_user_allowed(UserPolicyType::USERS_READ))
                ->add_var("authwrite", $this->auth->is_user_allowed(UserPolicyType::USERS_WRITE))
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->response->location(UrlType::ERROR_FORBIDDEN);
        }
        catch (Exception $e) {
            $this->logerr($e->getMessage(), "userscontroller.index");
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
            $search = SF::get_callable(UsersSearchService::class, $this->request->get_get());
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

}//UsersSearchController
