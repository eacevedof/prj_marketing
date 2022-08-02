<?php
namespace App\Open\TermsConditions\Infrastructure\Controllers;

use App\Open\Business\Application\BusinessSpaceService;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\TermsConditions\Application\TermsConditionsInfoService;
use App\Shared\Domain\Enums\RequestType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\PageType;
use \Exception;

final class TermsConditionsInfoController extends OpenController
{
    public function index(): void
    {
        try {
            $terms = SF::get_callable(TermsConditionsInfoService::class)();
            $this->set_layout("open/mypromos/info")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", [])
                ->add_var("result", $terms)
                ->render_nv();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", [])
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", [])
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", [])
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }

    public function promotion(string $promoslug): void
    {
        $istest = ($this->request->get_get("mode", "")==="test");
        $space = SF::get(BusinessSpaceService::class, ["_test_mode" => $istest])->get_data_by_promotion_slug($promoslug);
        if (!$space)
            $this->add_header($code = ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", __("{0} not found!", __("Promotion")))
                ->add_var("code", $code)
                ->render_nv();
        try {
            $terms = SF::get(TermsConditionsInfoService::class, [
                    "promoslug" => $promoslug,
                    RequestType::LANG => $this->request->get_request(RequestType::LANG, "")
                ])->get_by_promotion();

            $this->set_layout("open/mypromos/info")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("result", $terms)
                ->render_nv();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }
}



