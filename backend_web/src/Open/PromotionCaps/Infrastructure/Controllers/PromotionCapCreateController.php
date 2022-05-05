<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Picklist\Application\PicklistService;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PromotionCaps\Application\PromotionCapInfoService;
use App\Shared\Domain\Enums\PageType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapCreateController extends OpenController
{
    public function create(string $businessslug, string $promotionslug): void
    {
        $picklist = SF::get(PicklistService::class);
        try {
            $business = SF::get_callable(PromotionCapInfoService::class, [
                "businessslug" => trim($businessslug),
                "promotionslug" => trim($promotionslug),
                "mode" => $this->request->get_get("mode", "")
            ]);
            $result = $business();

            $this->set_layout("open/promotioncaps")
                ->add_var(PageType::TITLE, $title = htmlentities($result["promotion"]["description"] ?? $businessslug))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $result)
                ->add_var("languages", $picklist->get_languages())
                ->add_var("genders", $picklist->get_genders())
                ->add_var("countries", $picklist->get_countries());

            unset($business, $result, $title, $picklist, $businessslug, $promotionslug);
            $this->view->render();
        }
        catch (PromotionCapException $e) {
            $this->set_layout("open/promotioncaps")
                ->add_header($e->getCode())
                ->add_var(PageType::H1, __("Warning!"))
                ->add_var("error", $e->getMessage())
                ->render();
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



