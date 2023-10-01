<?php

namespace App\Open\TermsConditions\Infrastructure\Controllers;

use Exception;
use App\Open\Business\Application\BusinessSpaceService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Domain\Enums\{PageType, RequestType, ResponseType};
use App\Open\TermsConditions\Application\TermsConditionsInfoService;
use App\Shared\Infrastructure\Exceptions\{ForbiddenException, NotFoundException};

final class TermsConditionsInfoController extends OpenController
{
    public function index(): void
    {
        try {
            $terms = SF::getCallableService(TermsConditionsInfoService::class)();
            $this->setLayoutBySubPath("open/mypromos/info")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("result", $terms)
                ->renderLayoutOnly();
        } 
        catch (NotFoundException $e) {
            $this->addHeaderCode(ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        } 
        catch (ForbiddenException $e) {
            $this->addHeaderCode(ResponseType::FORBIDDEN)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        } 
        catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }

    public function promotion(string $promotionSlug): void
    {
        $isTestMode = ($this->requestComponent->getGet("mode", "") === "test");
        $space = SF::getInstanceOf(BusinessSpaceService::class, ["_test_mode" => $isTestMode])->getDataByPromotionSlug($promotionSlug);
        if (!$space) {
            $this->addHeaderCode($code = ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", __("{0} not found!", __("Promotion")))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }
        try {
            $terms = SF::getInstanceOf(TermsConditionsInfoService::class, [
                "promotionSlug" => $promotionSlug,
                RequestType::LANG => $this->requestComponent->getRequest(RequestType::LANG, "")
            ])->getSummarizedPortalAndPromotionConditions();

            $this->setLayoutBySubPath("open/mypromos/info")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("result", $terms)
                ->renderLayoutOnly();
        }
        catch (NotFoundException $e) {
            $this->addHeaderCode(ResponseType::NOT_FOUND)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        } 
        catch (ForbiddenException $e) {
            $this->addHeaderCode(ResponseType::FORBIDDEN)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        } 
        catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Terms & Conditions error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", $space)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }
}
