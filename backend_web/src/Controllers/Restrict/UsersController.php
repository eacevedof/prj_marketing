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
        $this->add_var(Key::PAGE_TITLE, __("USERS"));

        if (!$this->auth->is_user_allowed(Action::DASHBOARD_READ)) {
           $this->render_error([
               "h1"=>__("Unauthorized")
           ],"/error/403");
        }

        $this->render([
            "h1" => __("USERS")
        ]);
    }

    //@get
    public function info(string $uuid): void
    {

    }

    public function detail(string $uuid): void
    {
        $this->add_var(Key::PAGE_TITLE, __("USERS - detail"));
        $this->render([
            "h1" => __("User detail {0}", $uuid)
        ]);
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
