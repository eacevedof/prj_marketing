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
use TheFramework\Helpers\HelperJson;
use App\Traits\JsonTrait;

final class UsersController extends RestrictController
{
    use JsonTrait;

    public function index(?string $page=null): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_READ))
            $this->location(UrlType::FORBIDDEN);

        $this->add_var(KeyType::PAGE_TITLE, __("USERS - list"));
        $this->render([
            "h1" => __("Users")
        ]);
    }

    public function create(): void
    {
        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE)) {
            $this->render_error([
                "h1"=>__("Unauthorized")
            ],"/error/403");
        }
        $this
            ->add_var(KeyType::KEY_CSRF, $this->csrf->get_token())
            ->render_nl([
                "h1" => __("User create ^^")
            ]);
    }

    //@post
    public function insert(): void
    {
        if (!$this->csrf->is_valid($csrf = $this->_get_csrf())) {
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();
        }

        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE))
            $this->_get_json()->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([__("Not allowed to perform this operation")])
                ->show();

        try {
            $insert = SF::get_callable("Restrict\Users\UsersInsert", $this->get_post());
            $id = $insert();
            $this->_get_json()->set_payload([
                "message"=>__("auth ok"),
                "result" => ["id"=>$id],
            ])->show();
        }
        catch (\Exception $e)
        {
            $this->logerr($e->getMessage(),"UsersController.search");
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

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

    }

    public function detail(string $uuid): void
    {
        $this->add_var(KeyType::PAGE_TITLE, __("USERS - detail"));
        if (!$this->auth->is_user_allowed(ActionType::USERS_WRITE)) {
            $this->render_error([
                "h1"=>__("Unauthorized")
            ],"/error/403");
        }

        $this->render([
            "h1" => __("User detail {0}", $uuid)
        ]);
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

    
    //@post
    public function update(string $uuid): void
    {

    }

    //@get
    public function remove(string $uuid): void
    {

    }

}//UsersController
