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
        try {
            $business = SF::get_callable(UserCapPointsService::class, [
                "businessuuid" => trim($businessuuid),
                "capuseruuid" => trim($capuseruuid),
            ]);
            $result = $business();

            $this->set_layout("open/promotioncaps")
                ->add_var(PageType::TITLE, $title = htmlentities($result["business_name"] ?? ""))
                ->add_var(PageType::H1, $title)
                ->add_var("username", $result["username"])
                ->add_var("result", $result["result"]);

            unset($business, $result, $title, $businessuuid, $capuseruuid);
            $this->view->render();
        }
        catch (PromotionCapException $e) {
            $this->set_layout("open/promotioncaps")
                ->add_header($e->getCode())
                ->add_var(PageType::H1, __("Error!"))
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



