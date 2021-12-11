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
use App\Enums\ActionType;
use App\Enums\ExceptionType;
use App\Enums\KeyType;
use App\Enums\ProfileType;
use App\Enums\UrlType;
use App\Factories\ServiceFactory as SF;
use App\Services\Common\PicklistService;
use App\Services\Restrict\Users\UsersInfoService;
use App\Services\Restrict\Users\UsersUpdateService;
use TheFramework\Helpers\HelperJson;
use App\Traits\JsonTrait;

final class UsersController extends RestrictController
{
    use JsonTrait;
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get("Common\Picklist");
    }

    public function index(?string $page=null): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ))
            $this->location(UrlType::FORBIDDEN);

        $this->add_var(KeyType::PAGE_TITLE, __("USERS - list"))
            ->add_var("h1", __("USERS"))
            ->add_var("languages", $this->picklist->get_languages())
            ->add_var("profiles", $this->picklist->get_profiles())
            ->add_var("countries", $this->picklist->get_countries())
            ->add_var("dthelp", SF::get_callable("Restrict\Users\UsersSearch", [])->get_datatable())
            ->render();
    }

    //@get
    public function search(): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ))
            $this->location(UrlType::FORBIDDEN);

        $search = SF::get_callable("Restrict\Users\UsersSearch", $this->get_get());
        try {
            $result = $search();
            $this->_get_json()->set_payload([
                "message"  => __("auth ok"),
                "result"   => $result["result"],
                "filtered" => $result["total"],
                "total"    => $result["total"],
            ])->show();
        }
        catch (\Exception $e)
        {
            $this->logerr($e->getMessage(),"UsersController.search");
            $this->_get_json()->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

    //@modal
    public function create(): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE)) {
            $this->add_var("h1",__("Unauthorized"))
                ->set_template("/error/403")
                ->render_nl();
        }

        $this->add_var(KeyType::KEY_CSRF, $this->csrf->get_token())
            ->add_var("h1",__("New user"))
            ->add_var("profiles", $this->picklist->get_profiles())
            ->add_var("parents", $this->picklist->get_users_by_profile(ProfileType::BUSINESS_MANAGER))
            ->add_var("countries", $this->picklist->get_countries())
            ->add_var("languages", $this->picklist->get_languages())
            ->render_nl();
    }

    //@post
    public function insert(): void
    {
        if (!$this->csrf->is_valid($this->_get_csrf())) {
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();
        }

        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE))
            $this->_get_json()->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([__("Not allowed to perform this operation")])
                ->show();

        /**
         * @var UsersInsertService
         */
        $service = SF::get_callable("Restrict\Users\UsersInsert", $this->get_post());
        try {
            $result = $service();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully created"),
                "result" => $result,
            ])->show();
        }
        catch (\Exception $e)
        {
            if ($service->is_error()) {
                $this->_get_json()->set_code($e->getCode())
                    ->set_error([["fields_validation" =>$service->get_errors()]])
                    ->show();
            }
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

    //@modal
    public function info(string $uuid): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ))
            $this->location(UrlType::FORBIDDEN);

        $this->add_var(KeyType::PAGE_TITLE, __("USERS - info"));
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ)) {
            $this->render_error([
                "h1"=>__("Unauthorized")
            ],"/error/403");
        }

        /**
         * @var UsersInfoService $service
         */
        $service = SF::get_callable("Restrict\Users\UsersInfo", [$uuid]);
        try {
            $result = $service();
            $this->add_var("h1",__("User info"))
                ->add_var("uuid",$uuid)
                ->add_var("result", $result)
                ->render_nl();
        }
        catch (\Exception $e)
        {
            $this->add_var("h1", $e->getMessage())
                ->render_nl();
        }
    }

    //@modal
    public function edit(string $uuid): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE)) {
            $this->add_var("h1",__("Unauthorized"))
                ->set_template("/error/403")
                ->render_nl();
        }

        try {
            /**
             * @var UsersInfoService
             */
            $service = SF::get("Restrict\Users\UsersInfo", [$uuid]);
            $result = $service->get_edit();
            $this->add_var(KeyType::KEY_CSRF, $this->csrf->get_token())
                ->add_var("h1",__("Edit user {0}", $uuid))
                ->add_var("uuid", $uuid)
                ->add_var("result", $result)
                ->add_var("profiles", $this->picklist->get_profiles())
                ->add_var("parents", $this->picklist->get_users_by_profile(ProfileType::BUSINESS_MANAGER))
                ->add_var("countries", $this->picklist->get_countries())
                ->add_var("languages", $this->picklist->get_languages())
                ->render_nl();
        }
        catch (\Exception $e)
        {
            $this->add_var("h1",$e->getMessage())
                ->set_template("/error/404")
                ->render_nl();
        }
    }

    //@post
    public function update(string $uuid): void
    {
        if(!($uuid = trim($uuid)))
            $this->_get_json()->set_code(HelperJson::CODE_BAD_REQUEST)
            ->set_error([__("No code provided")])
            ->show();

        if (!$this->csrf->is_valid($this->_get_csrf())) {
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();
        }

        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE))
            $this->_get_json()->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([__("Not allowed to perform this operation")])
                ->show();

        /**
         * @var UsersUpdateService
         */
        $request = array_merge(["uuid"=>$uuid], $this->get_post());
        $service = SF::get_callable("Restrict\Users\UsersUpdate", $request);
        try {
            $result = $service();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully created"),
                "result" => $result,
            ])->show();
        }
        catch (\Exception $e)
        {
            if ($service->is_error()) {
                $this->_get_json()->set_code($e->getCode())
                    ->set_error([["fields_validation" =>$service->get_errors()]])
                    ->show();
            }
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

    //@delete
    public function remove(string $uuid): void
    {
        if(!($uuid = trim($uuid)))
            $this->_get_json()->set_code(HelperJson::CODE_BAD_REQUEST)
                ->set_error([__("No code provided")])
                ->show();

        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE))
            $this->_get_json()->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([__("Not allowed to perform this operation")])
                ->show();

        /**
         * @var UsersUpdateService $service
         */
        $service = SF::get_callable("Restrict\Users\UsersDelete", ["uuid"=>$uuid]);
        try {
            $result = $service();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully created"),
                "result" => $result,
            ])->show();
        }
        catch (\Exception $e)
        {
            if ($service->is_error()) {
                $this->_get_json()->set_code($e->getCode())
                    ->set_error([["fields_validation" =>$service->get_errors()]])
                    ->show();
            }
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

}//UsersController
