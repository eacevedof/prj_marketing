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
use App\Open\PromotionCaps\Application\PromotionCapsCancelService;

final class PromotionCapCancelController extends OpenController
{
    public function cancel(string $businessSlug, string $subscriptionUuid): void
    {
        if (!($businessSlug && $subscriptionUuid)) {
            $this->addHeaderCode(ResponseType::BAD_REQUEST)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", __("Missing partner and/or subscription code"))
                ->addGlobalVar("code", ResponseType::BAD_REQUEST)
                ->renderLayoutOnly();
        }

        $isTestMode = ($this->requestComponent->getGet("mode", "") === "test");
        $space = SF::getInstanceOf(BusinessSpaceService::class, ["_test_mode" => $isTestMode])->getDataByPromotionCapByPromotionCapUuid($subscriptionUuid);
        if (!$space) {
            $this->addHeaderCode($code = ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", __("{0} not found!", __("Subscription")))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        try {
            $insert = SF::getCallableService(PromotionCapsCancelService::class, [
                "promotionuuid" => $space["promocode"] ?? "",
                "subscriptionuuid" => $subscriptionUuid,
                "_test_mode" => $this->requestComponent->getGet("mode", "") === "test",
            ]);
            $result = $insert();
            $this->setLayoutBySubPath("open/mypromos/success")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription cancellation successfully done!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("success", [
                    ["p" => __("<b>{0}</b>. You have successfully cancelled your subscription to <b>“{1}“</b>", $result["username"], $result["promotion"])],
                ]);
            unset($insert, $result, $subscriptionUuid);
            $this->view->renderLayoutOnly();
        } catch (PromotionCapException $e) {
            $this->addHeaderCode($e->getCode())
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        } catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }
}
