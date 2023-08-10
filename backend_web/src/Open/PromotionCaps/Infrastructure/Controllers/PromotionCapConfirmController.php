<?php
/**
 * @link eduardoaf.com
 */

namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Open\Business\Application\BusinessSpaceService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use App\Open\PromotionCaps\Application\PromotionCapsConfirmService;

final class PromotionCapConfirmController extends OpenController
{
    public function confirm(string $businessSlug, string $promotionCapUuid): void
    {
        if (!($businessSlug && $promotionCapUuid)) {
            $this->setLayoutBySubPath("open/mypromos/error")
                ->addHeaderCode($code = ResponseType::BAD_REQUEST)
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", __("Missing partner and/or subscription code"))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        $isTestMode = ($this->requestComponent->getGet("mode", "") === "test");
        $space = SF::getInstanceOf(BusinessSpaceService::class, ["_test_mode" => $isTestMode])->getDataByPromotionCapByPromotionCapUuid($promotionCapUuid);
        if (!$space) {
            $this->addHeaderCode($code = ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", __("{0} not found!", __("Subscription")))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        try {
            $insert = SF::getCallableService(PromotionCapsConfirmService::class, [
                "promotionuuid" => $space["promocode"] ?? "",
                "subscriptionuuid" => $promotionCapUuid,
                "_test_mode" => $isTestMode,
            ]);
            $result = $insert();
            $this->setLayoutBySubPath("open/mypromos/success")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription confirmation success!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("success", [
                    ["p" => __("<b>{0}</b>. You have successfully confirmed your subscription to <b>“{1}“</b>", $result["username"], $result["promotion"])],
                    ["p" => __("Please check your email inbox. You will receive a voucher code which you should show it at <b>{0}</b>", $result["business"])],
                ]);

            unset($insert, $result, $promotionCapUuid);
            $this->view->renderLayoutOnly();
        } catch (PromotionCapException $e) {
            $this->addHeaderCode($e->getCode())
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        } catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }
}
