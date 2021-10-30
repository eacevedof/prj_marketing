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
use App\Services\Restrict\LoginService;
use App\Factories\ServiceFactory as SF;


final class LoginController extends RestrictController
{
    private LoginService $login;

    public function index(): void
    {
        $this->add_var("pagetitle", "LOGIN")
            ->add_var("login", $this->login)
        ;
        $this->render();
    }

    public function access(): void
    {
        $this->login = SF::get("Restrict\LoginService");
    }

}//LoginController
