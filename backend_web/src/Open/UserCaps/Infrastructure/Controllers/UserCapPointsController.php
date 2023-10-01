<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 */

namespace App\Open\UserCaps\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Open\Business\Application\BusinessSpaceService;
use App\Open\UserCaps\Application\UserCapPointsService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class UserCapPointsController extends OpenController
{
    public function index(string $businessSlug, string $capuseruuid): void
    {
        if (!($businessSlug && $capuseruuid)) {
            $this->setLayoutBySubPath("open/mypromos/error")
                ->addHeaderCode($code = ResponseType::BAD_REQUEST)
                ->addGlobalVar(PageType::TITLE, $title = __("Accumulated points error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", __("Missing partner and/or user code"))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        $istest = ($this->requestComponent->getGet("mode", "") === "test");
        $space = SF::getInstanceOf(BusinessSpaceService::class, ["_test_mode" => $istest])->getDataByPromotionCapUser($capuseruuid);
        if (!$space) {
            $this->addHeaderCode($code = ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Accumulated points error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", __("{0} not found!", __("Subscriber")))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        try {
            $userpoints = SF::getCallableService(UserCapPointsService::class, [
                "businessuuid" => $space["businesscode"] ?? "",
                "capuseruuid" => $capuseruuid,
            ]);
            $result = $userpoints();

            $title = $result["business_name"] ?? "";
            $title = __("Accumulated points of “{0}“ at “{1}“", $result["username"] ?? $result["email"], $title);
            $this->setLayoutBySubPath("open/mypromos/success")
                ->addGlobalVar(PageType::TITLE, $title)
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("total", $result["total_points"])
                ->addGlobalVar("result", $result["result"]);

            unset($userpoints, $result, $title, $businessuuid, $capuseruuid);
            $this->render();
        } catch (PromotionCapException $e) {
            $this->addHeaderCode($e->getCode())
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Accumulated points error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->render();
        } catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Accumulated points error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->render();
        }
    }
}
