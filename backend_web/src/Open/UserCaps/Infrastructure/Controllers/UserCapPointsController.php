<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 */
namespace App\Open\UserCaps\Infrastructure\Controllers;

use App\Picklist\Application\PicklistService;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\UserCaps\Application\UserCapPointsService;
use App\Shared\Domain\Enums\PageType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class UserCapPointsController extends OpenController
{
    public function index(string $businessuuid, string $capuseruuid): void
    {
        $picklist = SF::get(PicklistService::class);
        try {
            $business = SF::get_callable(UserCapPointsService::class, [
                "businessslug" => trim($businessuuid),
                "promotionslug" => trim($capuseruuid),
                "mode" => $this->request->get_get("mode", "")
            ]);
            $result = $business();

            $this->set_layout("open/promotioncaps")
                ->add_var(PageType::TITLE, $title = htmlentities($result["promotion"]["description"] ?? $businessuuid))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $result)
                ->add_var("languages", $picklist->get_languages())
                ->add_var("genders", $picklist->get_genders())
                ->add_var("countries", $picklist->get_countries());

            unset($business, $result, $title, $picklist, $businessuuid, $capuseruuid);
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



