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

use App\Components\Auth\AuthComponent;
use App\Controllers\AppController;
use App\Traits\ViewTrait;
use App\Traits\SessionTrait;
use App\Enums\Url;

abstract class RestrictController extends AppController
{
    use ViewTrait;
    use SessionTrait;

    protected AuthComponent $auth;
    protected ?array $authuser = null;

    public function __construct()
    {
        $this->auth = new AuthComponent();
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
        $this->location(Url::ON_LOGOUT);
    }
}//RestrictController
