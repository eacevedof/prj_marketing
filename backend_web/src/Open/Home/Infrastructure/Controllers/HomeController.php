<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\Home\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\CsrfService;
use App\Shared\Domain\Enums\PageType;

final class HomeController extends OpenController
{
    public function index(): void
    {
        $this->set_layout("open/mypromos/home")
            ->add_var(PageType::TITLE, $title = __("My Promotions"))
            ->add_var(PageType::H1, $title)
            ->add_var(PageType::CSRF, SF::get(CsrfService::class)->get_token())
            ->add_var("space", [])
            //->cache()
            ->render();
    }
}



