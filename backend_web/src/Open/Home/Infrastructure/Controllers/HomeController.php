<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Open\Home\Infrastructure\Controllers\BusinessController
 * @file HomeController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Open\Home\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Domain\Enums\PageType;

final class HomeController extends OpenController
{
    public function index(): void
    {
        $this->set_layout("open/mypromos/home")
            ->add_var(PageType::TITLE, $title = __("My Promotions"))
            ->add_var(PageType::H1, $title)
            //->cache()
            ->render();
    }
}



