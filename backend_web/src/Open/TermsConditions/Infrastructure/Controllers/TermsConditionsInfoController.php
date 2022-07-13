<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Open\TermsConditions\Infrastructure\Controllers\TermsConditionsController
 * @file TermsConditionsController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
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
            $this->set_layout("open/business")
                ->add_var(PageType::TITLE, $title = __("Terms & Conditions"))
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


}//TermsConditionsController



