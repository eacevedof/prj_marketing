<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Open\Business\Infrastructure\Controllers\BusinessController
 * @file BusinessController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */

namespace App\Open\Business\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\Business\Application\{
    BusinessSpacePageService,
    BusinessSpaceService
};

final class BusinessController extends OpenController
{
    public function index(string $businessSlug): void
    {
        if (!$businessSlug) {
            $this->setLayoutBySubPath("open/mypromos/error")
                ->addHeaderCode($code = ResponseType::BAD_REQUEST)
                ->addGlobalVar(PageType::TITLE, $title = __("Partner space error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", __("Missing partner"))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        try {
            $space = SF::getInstanceOf(BusinessSpaceService::class)->getBusinessDataByBusinessSlug($businessSlug);
            $this->setLayoutBySubPath("open/mypromos/business")
                ->addGlobalVar(PageType::TITLE, $title = $space["business"])
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar(
                    "result",
                    SF::getInstanceOf(BusinessSpacePageService::class)->getPageByBusinessSlug($businessSlug)
                )
                ->renderLayoutOnly();
        }
        catch (NotFoundException $e) {
            $this->addHeaderCode(ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Partner space error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
        catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Partner space error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }

}//BusinessController
