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
use App\Enums\Action;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        $this->add_var("pagetitle", "DASHBOARD");

        if (!$this->auth->is_user_allowed(Action::DASHBOARD_READ)) {
           $this->render_error([
               "h1"=>__("Unauthorized")
           ],"/error/403");
        }
        $this->render([
            "h1" => __("Dashboard")
        ]);
    }

}//DashboardController
