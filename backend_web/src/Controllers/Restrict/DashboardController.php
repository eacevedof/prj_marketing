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
use App\Enums\Key;
use App\Enums\Url;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        if (!$this->auth->is_user_allowed(Action::DASHBOARD_READ))
            $this->location(Url::FORBIDDEN);

        $this->add_var(Key::PAGE_TITLE, __("DASHBOARD"));
        $this->render([
            "h1" => __("Dashboard")
        ]);
    }

}//DashboardController
