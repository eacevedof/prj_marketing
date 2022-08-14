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

use App\Restrict\BusinessData\Application\BusinessDataDisabledService;
use App\Shared\Infrastructure\Controllers\AppController;
use App\Shared\Infrastructure\Traits\SessionTrait;
use App\Shared\Infrastructure\Traits\RequestTrait;
use App\Shared\Infrastructure\Traits\ViewTrait;
use App\Shared\Infrastructure\Traits\ResponseTrait;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\AuthService;
use App\Restrict\Auth\Application\CsrfService;
use App\Restrict\Login\Application\ModulesService;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Shared\Domain\Enums\UrlType;

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

        $this->auth = SF::get_auth();
        $this->csrf = SF::get(CsrfService::class);

        $this->_load_view()->set_layout("restrict/restrict");
        $this->add_var("authuser", $this->auth->get_user());
        $this->_add_topmenu();
        $this->_add_bowdisabled();
    }

    protected function _add_topmenu(): void
    {
        $service = SF::get_callable(ModulesService::class);
        $this->add_var("topmenu", $service->get_menu());
    }

    /**
     * Works only after __construct() execution
     */
    protected function _if_noauth_tologin(): void
    {
        if(!$this->auth->get_user()) {
            $redirect = $this->request->get_request_uri();
            $loginurl = Routes::url("login");
            if (strstr($redirect, "/restrict")) $loginurl = "$loginurl?redirect=".urlencode($redirect);
            $this->response->location($loginurl);
        }
    }

    protected function _add_bowdisabled(): void
    {
        $this->add_var("bowdisabled", []);
        if (!($iduser = $this->auth->get_user()["id"] ?? "")) return;
        if ($this->auth->is_system()) return;

        $this->add_var(
            "bowdisabled",
            SF::get(BusinessDataDisabledService::class)->get_disabled_data_by_user($this->auth->get_idowner($iduser))
        );
    }

}//RestrictController
