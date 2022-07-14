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
            $this->set_layout("open/open")
                ->add_var(PageType::TITLE, $title = __("Cookies Policy"))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $terms)
                ->render();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("404")
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("403")
                ->render_nl();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("500")
                ->render_nl();
        }
    }
}



