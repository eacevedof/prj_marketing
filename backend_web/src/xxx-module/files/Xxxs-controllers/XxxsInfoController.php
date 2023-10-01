<?php
/**
 * @author Module Builder.
 * @link eduardoaf.com
 * @name App\Restrict\Xxxs\Infrastructure\Controllers\XxxsInfoController
 * @file XxxsInfoController.php v1.0.0
 * @date %DATE% SPAIN
 * @observations
 */

namespace App\Restrict\Xxxs\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Xxxs\Application\XxxsInfoService;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Exceptions\{ForbiddenException, NotFoundException};

final class XxxsInfoController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
        $this->picklist = SF::getInstanceOf(PicklistService::class);
    }

    //@modal
    public function info(string $uuid): void
    {
        $this->addGlobalVar(PageType::TITLE, __("Xxx info"))
            ->addGlobalVar(PageType::H1, __("Xxx info"))
            ->addGlobalVar("ismodal", 1);

        try {
            $info = SF::getCallableService(XxxsInfoService::class, [$uuid]);
            $result = $info();
            $this->addGlobalVar("uuid", $uuid)
                ->addGlobalVar("result", $result)
                ->addGlobalVar("issystem", $this->authService->hasAuthUserSystemProfile())
                ->renderViewOnly();
        } catch (NotFoundException $e) {
            $this->addHeaderCode(ResponseType::NOT_FOUND)
                ->addGlobalVar(PageType::H1, $e->getMessage())
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("404")
                ->renderViewOnly();
        } catch (ForbiddenException $e) {
            $this->addHeaderCode(ResponseType::FORBIDDEN)
                ->addGlobalVar(PageType::H1, $e->getMessage())
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("403")
                ->renderViewOnly();
        } catch (Exception $e) {
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->addGlobalVar(PageType::H1, $e->getMessage())
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("500")
                ->renderViewOnly();
        }
    }

}//XxxsInfoController
