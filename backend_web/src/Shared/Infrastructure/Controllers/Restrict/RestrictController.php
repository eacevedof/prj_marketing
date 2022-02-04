<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\RestrictController
 * @file RestrictController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Shared\Infrastructure\Controllers\Restrict;

use App\Shared\Infrastructure\Controllers\AppController;
use App\Shared\Infrastructure\Traits\SessionTrait;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Traits\ViewTrait;
use App\Shared\Infrastructure\Traits\ResponseTrait;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Auth\Application\CsrfService;
use App\Restrict\Login\Application\ModulesService;

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

        $this->_load_view()->set_layout("restrict/restrict");

        $this->auth = SF::get_auth();
        $this->csrf = SF::get(CsrfService::class);
        $this->add_var("authuser", $this->auth->get_user());
        $this->_add_topmenu();
    }

    protected function _add_topmenu(): void
    {
        $service = SF::get_callable(ModulesService::class);
        $this->add_var("topmenu", $service->get_menu());
    }

}//RestrictController
