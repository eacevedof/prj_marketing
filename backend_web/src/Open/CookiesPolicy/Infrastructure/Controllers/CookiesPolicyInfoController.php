<?php
namespace App\Open\CookiesPolicy\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\CookiesPolicy\Application\CookiesPolicyInfoService;
use App\Shared\Domain\Enums\PageType;

final class CookiesPolicyInfoController extends OpenController
{
    public function index(): void
    {
        try {
            $terms = SF::get_callable(CookiesPolicyInfoService::class)();
            $this->set_layout("open/mypromos/info")
                ->add_var(PageType::TITLE, $title = __("Cookies Policy"))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $terms)
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Cookies Policy error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }
}



