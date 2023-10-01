<?php

namespace App\Open\CookiesPolicy\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\CookiesPolicy\Application\CookiesPolicyInfoService;

final class CookiesPolicyInfoController extends OpenController
{
    public function index(): void
    {
        try {
            $terms = SF::getCallableService(CookiesPolicyInfoService::class)();
            $this->setLayoutBySubPath("open/mypromos/info")
                ->addGlobalVar(PageType::TITLE, $title = __("Cookies Policy"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("result", $terms)
                ->renderLayoutOnly();
        } catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Cookies Policy error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }
}
