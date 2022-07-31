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
        if (!($businessslug && $promotionslug))
            $this->set_layout("open/mypromos/error")
                ->add_header($code = ResponseType::BAD_REQUEST)
                ->add_var(PageType::TITLE, $title = __("Subscription error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", __("Missing partner and/or promotion"))
                ->add_var("code", $code)
                ->render_nv();

        $picklist = SF::get(PicklistService::class);
        try {
            $promotioncap = SF::get_callable(PromotionCapInfoService::class, [
                "businessslug" => $businessslug,
                "promotionslug" => $promotionslug,
                "_test_mode" => $this->request->get_get("mode", "")==="test",
            ]);
            $promotioncap = $promotioncap();

            $this->set_layout("open/promotioncaps")
                ->add_var(PageType::TITLE, $title = htmlentities($result["promotion"]["description"] ?? $businessslug))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $promotioncap)
                ->add_var("languages", $picklist->get_languages())
                ->add_var("genders", $picklist->get_genders())
                ->add_var("countries", $picklist->get_countries());

            unset($promotioncap, $businessdata, $result, $title, $picklist, $businessslug, $promotionslug);
            $this->render();
        }
        catch (PromotionCapException $e) {
            $this->add_header($e->getCode())
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Unexpected error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }
}



