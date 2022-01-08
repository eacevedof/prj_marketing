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

use App\Factories\ServiceFactory as SF;
use App\Enums\PageType;
use App\Enums\UrlType;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        if(!$this->authuser)
            $this->response->location(UrlType::FORBIDDEN);

        $service = SF::get_callable("Restrict\Modules");
        $this
            ->add_var(PageType::TITLE, __("Dashboard"))
            ->add_var(PageType::H1, __("Dashboard"))
            ->add_var("modules", $service())
            ->render()
        ;
    }

}//DashboardController
