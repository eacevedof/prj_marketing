<?php
namespace App\Open\TermsConditions\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Traits\CookieTrait;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\TermsConditions\Application\TermsConditionsInfoService;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\CookieType;

final class TermsConditionsInfoController extends OpenController
{
    use CookieTrait;

    public function index(): void
    {
        try {
            $terms = SF::get_callable(TermsConditionsInfoService::class)();
            $this->set_layout("open/mypromos/info")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $terms)
                ->render_nv();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }

    public function promotion(string $promoslug): void
    {
        $this->_load_cookie();
        try {
            $terms = SF::get(
                TermsConditionsInfoService::class,
                [
                    "promoslug"=>$promoslug,
                    CookieType::LANG => $this->cookie->get_value(CookieType::LANG) ?? ""
                ]
            )->get_by_promotion();

            $this->set_layout("open/mypromos/info")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $terms)
                ->render_nv();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }
}



