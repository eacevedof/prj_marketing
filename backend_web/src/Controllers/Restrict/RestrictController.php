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

use App\Controllers\AppController;
use App\Traits\SessionTrait;
use App\Traits\RequestTrait;
use App\Traits\ViewTrait;
use App\Traits\ResponseTrait;
use App\Factories\ServiceFactory as SF;
use App\Services\Auth\AuthService;
use App\Services\Auth\CsrfService;

abstract class RestrictController extends AppController
{
    use SessionTrait;
    use RequestTrait;
    use ViewTrait;
    use ResponseTrait;

    protected AuthService $auth;
    protected CsrfService $csrf;

    /**
     * Builds request, response, auth, csrf, restrict-layout and toppmenu
     * add authuser to views
     */
    public function __construct()
    {
        $this->_load_request();
        $this->_load_response();

        $this->_load_view()->set_layout("restrict/restrict")->set_foldertpl("restrict");

        $this->auth = SF::get_auth();
        $this->csrf = SF::get("Auth\Csrf");
        $this->add_var("authuser", $this->auth->get_user());
        $this->_add_topmenu();
    }

    protected function _add_topmenu(): void
    {
        $service = SF::get_callable("Restrict\Modules");
        $this->add_var("topmenu", $service->get_menu());
    }

}//RestrictController
