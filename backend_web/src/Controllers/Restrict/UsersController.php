<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\RestrictController
 * @file RestrictController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict;

use App\Factories\ServiceFactory as SF;
use App\Services\Common\PicklistService;
use App\Services\Restrict\Users\UsersDeleteService;
use App\Services\Restrict\Users\UsersInfoService;
use App\Services\Restrict\Users\UsersUpdateService;
use TheFramework\Helpers\HelperJson;
use App\Enums\PolicyType;
use App\Enums\PageType;
use App\Enums\ProfileType;
use App\Enums\ResponseType;
use App\Enums\UrlType;
use App\Enums\ExceptionType;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\FieldsException;
use \Exception;

final class UsersController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get("Common\Picklist");
    }

    public function index(?string $page=null): void
    {
        try {
            $search = SF::get("Restrict\Users\UsersSearch");

            $this->add_var(PageType::TITLE, __("Users"))
                ->add_var(PageType::H1, __("Users"))
                ->add_var("languages", $this->picklist->get_languages())
                ->add_var("profiles", $this->picklist->get_profiles())
                ->add_var("countries", $this->picklist->get_countries())
                ->add_var("auth", $this->authuser)
                ->add_var("dthelp", $search->get_datatable())
                ->render();
        }
        catch (ForbiddenException $e){
            $this->response->location(UrlType::FORBIDDEN);
        }
    }

    //@get
    public function search(): void
    {
        if (!$this->request->is_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $search = SF::get_callable("Restrict\Users\UsersSearch", $this->request->get_get());
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
    }

    //@modal (creation form)
    public function create(): void
    {
        if (!$this->auth->is_user_allowed(PolicyType::USERS_WRITE)) {
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_template("/error/403")
                ->render_nl();
        }

        $this->add_var(PageType::CSRF, $this->csrf->get_token())
            ->add_var(PageType::H1,__("New user"))
            ->add_var("profiles", $this->picklist->get_profiles())
            ->add_var("parents", $this->picklist->get_users_by_profile(ProfileType::BUSINESS_OWNER))
            ->add_var("countries", $this->picklist->get_countries())
            ->add_var("languages", $this->picklist->get_languages())
            ->render_nl();
    }

    //@post
    public function insert(): void
    {
        if (!$this->request->is_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        if (!$this->csrf->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ResponseType::FORBIDDEN)
                ->set_error([__("Invalid CSRF token")])
                ->show();
        
        try {
            $insert = SF::get_callable("Restrict\Users\UsersInsert", $this->request->get_post());
            $result = $insert();
            $this->_get_json()->set_payload([
                "message" => __("User successfully created"),
                "result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([
                    ["fields_validation" => $insert->get_errors()]
                ])
                ->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }

    }//insert

    //@modal
    public function info(string $uuid): void
    {
         $this->add_var(PageType::TITLE, __("User info"))
             ->add_var(PageType::H1, __("User info"))
             ->add_var("ismodal",1);

        try {
            $info = SF::get_callable("Restrict\Users\UsersInfo", [$uuid]);
            $result = $info();
            $this->add_var("uuid", $uuid)
                ->add_var("result", $result)
                ->render_nl();
        }
        catch (NotFoundException $e) {
            $this->add_var(PageType::H1, $e->getMessage())
                ->set_template("/error/404")
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->add_var(PageType::H1, $e->getMessage())
                ->set_template("/error/403")
                ->render_nl();
        }
        catch (Exception $e) {
            $this->add_var(PageType::H1, $e->getMessage())
                ->set_template("/error/500")
                ->render_nl();
        }
    }

    //@modal
    public function edit(string $uuid): void
    {
        if (!$this->auth->is_user_allowed(PolicyType::USERS_WRITE)) {
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_template("/error/403")
                ->render_nl();
        }

        $this->add_var("ismodal",1);
        try {
            $edit = SF::get("Restrict\Users\UsersInfo", [$uuid]);
            $result = $edit->get_for_edit();
            $this->add_var(PageType::TITLE, __("Edit user {0}", $uuid))
                ->add_var(PageType::H1, __("Edit user {0}", $uuid))
                ->add_var(PageType::CSRF, $this->csrf->get_token())
                ->add_var("uuid", $uuid)
                ->add_var("result", $result)
                ->add_var("profiles", $this->picklist->get_profiles())
                ->add_var("parents", $this->picklist->get_users_by_profile(ProfileType::BUSINESS_OWNER))
                ->add_var("countries", $this->picklist->get_countries())
                ->add_var("languages", $this->picklist->get_languages())
                ->render_nl();
        }
        catch (NotFoundException $e) {
            $this->add_var(PageType::TITLE, $e->getMessage())
                ->add_var(PageType::H1, $e->getMessage())
                ->set_template("/error/404")
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->add_var(PageType::TITLE, $e->getMessage())
                ->add_var(PageType::H1, $e->getMessage())
                ->set_template("/error/403")
                ->render_nl();
        }
        catch (Exception $e) {
            $this->add_var(PageType::TITLE, $e->getMessage())
                ->add_var(PageType::H1, $e->getMessage())
                ->set_template("/error/500")
                ->render_nl();
        }
    }//modal edit

    //@patch
    public function update(string $uuid): void
    {
        if (!$this->request->is_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        if (!$this->csrf->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();

        try {
            $request = array_merge(["uuid"=>$uuid], $this->request->get_post());
            $update = SF::get_callable("Restrict\Users\UsersUpdate", $request);
            $result = $update();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully created"),
                "result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([["fields_validation" => $update->get_errors()]])
                ->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }//patch

    //@delete
    public function remove(string $uuid): void
    {
        if (!$this->request->is_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $delete = SF::get_callable("Restrict\Users\UsersDelete", ["uuid"=>$uuid]);
            $result = $delete();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully removed"),
                "result" => $result,
            ])->show();
        }
        catch (Exception $e)
        {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

    //@undelete
    public function undelete(string $uuid): void
    {
        if (!$this->request->is_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();
        try {
            $delete = SF::get_callable("Restrict\Users\UsersDelete", ["uuid"=>$uuid]);
            $result = $delete->undelete();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully restored"),
                "result" => $result,
            ])->show();
        }
        catch (Exception $e)
        {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }
}//UsersController
