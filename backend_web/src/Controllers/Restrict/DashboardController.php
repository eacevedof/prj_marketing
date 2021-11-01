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
use TheFramework\Helpers\HelperJson;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        $this->add_var("pagetitle", "DASHBOARD");
        if (!$this->auth->is_user_allowed($this->sess_get("auth_user"),"restrict:read")) {
           $this->render([],"/error/403");
           die;
        }
        $this->render();
    }

}//DashboardController
