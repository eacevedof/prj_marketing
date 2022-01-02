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
use App\Factories\ServiceFactory as SF;
use App\Enums\UrlType;
use App\Services\Auth\AuthService;
use App\Services\Auth\CsrfService;
use App\Traits\ResponseTrait;

abstract class RestrictController extends AppController
{
    use SessionTrait;
    use RequestTrait;
    use ViewTrait;
    use ResponseTrait;

    protected CsrfService $csrf;
    protected AuthService $auth;
    protected ?array $authuser;

    /**
     * Builds request, response, auth, csrf, authuser, config-layout and toppmenu
     */
    public function __construct()
    {
        $this->_load_request();
        $this->_load_response();

        $this->auth = SF::get("Auth\Auth");
        $this->csrf = SF::get("Auth\Csrf");
        $this->authuser = $this->auth->get_user();

        $this->add_var("authuser", $this->authuser);
        $this->set_layout("restrict/restrict");
        $this->_add_topmenu();
    }

    public function logout(): void
    {
        $this->_sessioninit()->destroy();
        $this->response->location(UrlType::ON_LOGOUT);
    }

    protected function _add_topmenu(): void
    {
        $service = SF::get_callable("Restrict\Modules");
        $this->add_var("topmenu", $service->get_menu());
    }

}//RestrictController
