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
use App\Enums\UrlType;
use App\Factories\ServiceFactory as SF;
use App\Services\Restrict\Users\UsersInfoService;
use TheFramework\Helpers\HelperJson;
use App\Traits\JsonTrait;

final class UsersController extends RestrictController
{
    use JsonTrait;

    public function index(?string $page=null): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ))
            $this->location(UrlType::FORBIDDEN);

        $this
            ->add_var(KeyType::PAGE_TITLE, __("USERS - list"))
            ->render([
                "h1" => __("Users")
            ]);
    }

    //@modal
    public function create(): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE)) {
            $this->add_var(
                "h1",__("Unauthorized"))
                ->set_template("/error/403")
                ->render_nl();
        }

        $this->add_var(KeyType::KEY_CSRF, $this->csrf->get_token())
            ->add_var("h1",__("New user"))
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
         * @var UsersInfoService
         */
        $service = SF::get_callable("Restrict\Users\UsersInfo", [$uuid]);
        try {
            $result = $service();
            $this->add_var("h1",__("User info"))
                ->add_var("uuid",$uuid)
                ->add_var("userinfo", $result)
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
            $item = $service->get_edit();
            $this->add_var(KeyType::KEY_CSRF, $this->csrf->get_token())
                ->add_var("h1",__("Edit user {0}", $uuid))
                ->add_var("uuid", $uuid)
                ->add_var("item", $item)
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
        $service = SF::get_callable("Restrict\Users\UsersUpdate", $this->get_post());
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

    //@get
    public function search(): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ))
            $this->location(UrlType::FORBIDDEN);

        $search = SF::get_callable("Restrict\Users\UsersSearch", $this->get_get());
        try {
            $result = $search();
            $this->_get_json()->set_payload([
                "message"=>__("auth ok"),
                "result" => $result["data"],
                "recordsFiltered" => $result["recordsFiltered"],
                "recordsTotal" => $result["recordsTotal"],
                "draw" => 3
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

    //@get
    public function remove(string $uuid): void
    {

    }

}//UsersController
