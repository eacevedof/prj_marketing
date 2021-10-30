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
use App\Services\Restrict\LoginService;


final class LoginController extends RestrictController
{
    private LoginService $login;

    public function index(): void
    {
        $this->login = SF::get("Restrict\LoginService");
        $this->add_var("pagetitle", "LOGIN")->add_var("login", $this->login);
        $this->render();
    }

}//LoginController
