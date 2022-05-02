<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\Business\Application\BusinessInfoService;
use App\Shared\Domain\Enums\PageType;

final class PromotionCapsInsertController extends OpenController
{

    public function insert(string $promotionuuid): void
    {
        try {
            $business = SF::get_callable(BusinessInfoService::class, [
                "businessslug" => trim($businessslug),
                "promotionslug" => trim($promotionslug),
                "mode" => $this->request->get_get("mode", "")
            ]);
            $business = $business();

            $this->set_layout("open/business")
                ->add_var(PageType::TITLE, $title = htmlentities($business["promotion"]["description"] ?? $businessslug))
                ->add_var(PageType::H1, $title)
                ->add_var("business", $business)
                ->add_var( "languages", [])
                ->add_var( "genders", [])
                ->add_var( "countries", [])
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



