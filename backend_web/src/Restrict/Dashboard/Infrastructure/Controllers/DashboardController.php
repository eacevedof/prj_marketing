<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\RestrictController
 * @file RestrictController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Restrict\Dashboard\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Login\Application\ModulesService;
use App\Shared\Domain\Enums\PageType;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        $this->_if_noauth_tologin();

        $modules = SF::get_callable(ModulesService::class);
        $this->add_var(PageType::TITLE, __("Dashboard x"))
            ->add_var(PageType::H1, __("Dashboard"))
            ->add_var("modules", $modules())
            ->add_var("bowdisabled",)
            ->render();
    }

}//DashboardController
