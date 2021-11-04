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
use App\Enums\Action;
use App\Enums\Key;

final class UsersController extends RestrictController
{
    public function index(): void
    {
        $this->add_var(Key::PAGE_TITLE, __("USERS - list"));

        if (!$this->auth->is_user_allowed(Action::USERS_READ)) {
           $this->render_error([
               "h1"=>__("Unauthorized")
           ],"/error/403");
        }

        $this->render([
            "h1" => __("USERS")
        ]);
    }

/*
["url"=>"/restrict/users/:uuid/info","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"info"],
    ["url"=>"/restrict/users/:uuid/update","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"update", "allowed"=>["post"]],
    ["url"=>"/restrict/users/:uuid/delete","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"remove", "allowed"=>["url"]],
    ["url"=>"/restrict/users/:page/search","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"search"],
    ["url"=>"/restrict/users/insert","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"insert"],
    ["url"=>"/restrict/users/:uuid","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"detail"],
    ["url"=>"/restrict/users","controller"=>"App\Controllers\Restrict\UsersController", "method"=>"index"],
 * */

    public function info(string $uuid): void
    {
        $this->add_var(Key::PAGE_TITLE, __("USERS - info"));
        if (!$this->auth->is_user_allowed(Action::USERS_READ)) {
            $this->render_error([
                "h1"=>__("Unauthorized")
            ],"/error/403");
        }

    }

    public function detail(string $uuid): void
    {
        $this->add_var(Key::PAGE_TITLE, __("USERS - detail"));
        if (!$this->auth->is_user_allowed(Action::USERS_WRITE)) {
            $this->render_error([
                "h1"=>__("Unauthorized")
            ],"/error/403");
        }

        $this->render([
            "h1" => __("User detail {0}", $uuid)
        ]);
    }

    //@get
    public function search(string $page): void
    {

    }

    //@post
    public function insert(): void
    {

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
