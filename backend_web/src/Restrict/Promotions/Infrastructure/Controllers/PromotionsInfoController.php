<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInfoController
 * @file PromotionsInfoController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Promotions\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Promotions\Application\PromotionsInfoService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Exceptions\{ForbiddenException, NotFoundException};

final class PromotionsInfoController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::getInstanceOf(PicklistService::class);
    }

    //@modal
    public function info(string $uuid): void
    {
        $this->addGlobalVar(PageType::TITLE, __("Promotion info"))
            ->addGlobalVar(PageType::H1, __("Promotion info"))
            ->addGlobalVar("ismodal", 1);

        try {
            $info = SF::getCallableService(PromotionsInfoService::class, [$uuid]);
            $result = $info();
            $this->addGlobalVar("uuid", $uuid)
                ->addGlobalVar("result", $result)
                ->addGlobalVar("statspermission", $this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTION_STATS_READ))
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

}//PromotionsInfoController
