<?php

namespace App\Open\PrivacyPolicy\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\PrivacyPolicy\Application\PrivacyPolicyInfoService;

final class PrivacyPolicyInfoController extends OpenController
{
    public function index(): void
    {
        try {
            $terms = SF::getCallableService(PrivacyPolicyInfoService::class)();
            $this->setLayoutBySubPath("open/mypromos/info")
                ->addGlobalVar(PageType::TITLE, $title = __("Privacy Policy"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("result", $terms)
                ->renderLayoutOnly();
        } catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setPartLayout("open/mypromos/error")
                ->addGlobalVar(PageType::TITLE, $title = __("Privacy Policy error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("space", [])
                ->addGlobalVar("error", $e->getMessage())
                ->addGlobalVar("code", $e->getCode())
                ->renderLayoutOnly();
        }
    }
}
