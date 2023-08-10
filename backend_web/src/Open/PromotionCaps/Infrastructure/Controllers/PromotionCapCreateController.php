<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 */

namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Open\Business\Application\BusinessSpaceService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\PromotionCaps\Application\PromotionCapInfoService;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapCreateController extends OpenController
{
    public function create(string $businessSlug, string $promotionSlug): void
    {
        if (!($businessSlug && $promotionSlug)) {
            $this->setLayoutBySubPath("open/mypromos/error")
                ->addHeaderCode($code = ResponseType::BAD_REQUEST)
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", __("Missing partner and/or promotion"))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        $isTestMode = ($this->requestComponent->getGet("mode", "") === "test");
        $space = SF::getInstanceOf(BusinessSpaceService::class, ["_test_mode" => $isTestMode])->getDataByPromotionSlug($promotionSlug);
        if (!$space) {
            $this->addHeaderCode($code = ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", __("{0} not found!", __("Promotion")))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        try {
            $promotionCapInfoService = SF::getCallableService(PromotionCapInfoService::class, [
                "businessSlug" => $businessSlug,
                "promotionSlug" => $promotionSlug,
                "_test_mode" => $this->requestComponent->getGet("mode", "") === "test",
            ]);

            $promotionCapInfoService = $promotionCapInfoService();
            $promotion = $promotionCapInfoService["promotion"]["description"] ?? "";
            $title = $promotion ? __("Promotion: {0}", $promotion) : $businessSlug;

            $picklistService = SF::getInstanceOf(PicklistService::class);
            $this->setLayoutBySubPath("open/promotioncaps")
                ->addGlobalVar(PageType::TITLE, $title)
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("result", $promotionCapInfoService)
                ->addGlobalVar("languages", $picklistService->getLanguages())
                ->addGlobalVar("genders", $picklistService->getGenders())
                ->addGlobalVar("countries", $picklistService->getCountries());

            unset($promotionCapInfoService, $result, $title, $picklistService, $businessSlug, $promotionSlug);
            $this->render();
        }
        catch (PromotionCapException $e) {
            $this->addHeaderCode($e->getCode())
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("An unexpected error occurred!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->render();
        }
    }
}
