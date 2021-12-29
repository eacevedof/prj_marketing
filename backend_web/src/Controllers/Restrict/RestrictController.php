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
use App\Traits\RequestTrait;
use App\Traits\ViewTrait;
use App\Traits\SessionTrait;
use App\Factories\ComponentFactory as CF;
use App\Factories\ServiceFactory as SF;
use App\Enums\UrlType;
use App\Components\Auth\AuthComponent;
use App\Services\Auth\CsrfService;

abstract class RestrictController extends AppController
{
    use ViewTrait;
    use SessionTrait;
    use RequestTrait;

    protected CsrfService $csrf;
    protected AuthComponent $auth;
    protected ?array $authuser;

    public function __construct()
    {
        $this->_load_request();
        $this->auth = CF::get("Auth\Auth");
        $this->csrf = SF::get("Auth\Csrf");
        $this->authuser = $this->auth->get_user();
        $this->add_var("authuser", $this->authuser);
        $this->set_layout("restrict/restrict");
        $this->_add_topmenu();
    }

    protected function location(string $url): void
    {
        header("Location: {$url}");
        exit();
    }

    public function logout(): void
    {
        $this->_sessioninit()->destroy();
        $this->location(UrlType::ON_LOGOUT);
    }

    protected function _add_topmenu(): void
    {
        $service = SF::get_callable("Restrict\Modules");
        $this->add_var("topmenu", $service->get_menu());
    }

}//RestrictController
