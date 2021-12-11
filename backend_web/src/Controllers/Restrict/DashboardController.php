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
use App\Enums\ActionType;
use App\Enums\KeyType;
use App\Enums\UrlType;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        if(!$this->authuser)
            $this->location(UrlType::FORBIDDEN);

        $this->add_var(KeyType::PAGE_TITLE, __("DASHBOARD"));
        $this->render([
            "h1" => __("Dashboard for user {0}", $this->authuser["description"])
        ]);
    }

}//DashboardController
