<?php
namespace App\Open\TermsConditions\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\TermsConditions\Application\TermsConditionsInfoService;
use App\Shared\Domain\Enums\PageType;

final class TermsConditionsInfoController extends OpenController
{
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
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }

    public function promotion(string $promoslug): void
    {
        try {
            $terms = SF::get(TermsConditionsInfoService::class, ["promoslug"=>$promoslug])->get_by_promotion();
            $this->set_layout("open/mypromos/home")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $terms)
                ->set_template("index")
                ->render();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }
}



