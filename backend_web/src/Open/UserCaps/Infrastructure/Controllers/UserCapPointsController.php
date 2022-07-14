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

            $title = htmlentities($result["business_name"] ?? "");
            $title = __("Accumulated points at {0} of {1}", $title, $result["username"]);
            $this->set_layout("open/success")
                ->add_var(PageType::TITLE, $title)
                ->add_var(PageType::H1, $title)
                ->add_var("success",  [
                    //["h2" => __("Hello {0}!", $result["username"])],
                    ["h3" => __("You have a total of {0} points", $result["total_points"])],
                ]);

            unset($business, $result, $title, $businessuuid, $capuseruuid);
            $this->render();
        }
        catch (PromotionCapException $e) {
            $this->add_header($e->getCode())
                ->set_layout("open/error")
                ->add_var(PageType::TITLE, $title = __("Accumulated points error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/error")
                ->add_var(PageType::TITLE, $title = __("Accumulated points error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }
}



