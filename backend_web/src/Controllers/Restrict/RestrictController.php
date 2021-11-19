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
use App\Factories\ComponentFactory as CF;
use App\Factories\ServiceFactory as SF;
use App\Services\Auth\CsrfService;
use App\Traits\ViewTrait;
use App\Traits\SessionTrait;
use App\Enums\UrlType;
use App\Components\Auth\AuthComponent;

abstract class RestrictController extends AppController
{
    use ViewTrait;
    use SessionTrait;

    protected CsrfService $csrf;
    protected AuthComponent $auth;
    protected ?array $authuser = null;

    public function __construct()
    {
        $this->auth = CF::get("Auth\Auth");
        $this->csrf = SF::get("Auth\Csrf");
        $this->authuser = $this->_sessioninit()->get("auth_user");
        $this->set_layout("restrict/restrict");
    }

    protected function location(string $url): void
    {
        header("Location: {$url}");
        exit();
    }

    public function logout(): void
    {
        $this->session->destroy();
        $this->location(UrlType::ON_LOGOUT);
    }
}//RestrictController
