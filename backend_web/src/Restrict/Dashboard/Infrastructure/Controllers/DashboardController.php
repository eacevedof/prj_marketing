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

use App\Shared\Domain\Enums\PageType;
use App\Restrict\Login\Application\ModulesService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        $this->_redirectToLoginIfNoAuthUser();

        $modules = SF::getCallableService(ModulesService::class);
        $this->addGlobalVar(PageType::TITLE, __("Dashboard"))
            ->addGlobalVar(PageType::H1, __("Dashboard"))
            ->addGlobalVar("modules", $modules())
            ->render();
    }

}//DashboardController
