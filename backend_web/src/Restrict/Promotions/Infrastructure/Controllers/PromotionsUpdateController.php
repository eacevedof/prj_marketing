<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsUpdateController
 * @file PromotionsUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Promotions\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Open\Business\Application\BusinessSpaceService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{ExceptionType, PageType, ResponseType};
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Restrict\Promotions\Application\{PromotionsInfoService, PromotionsUpdateService};
use App\Shared\Infrastructure\Exceptions\{FieldsException, ForbiddenException, NotFoundException};

final class PromotionsUpdateController extends RestrictController
{
    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
    }

    //@modal
    public function edit(string $uuid): void
    {
        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)) {
            $this->addGlobalVar(PageType::TITLE, __("Unauthorized"))
                ->addGlobalVar(PageType::H1, __("Unauthorized"))
                ->addGlobalVar("ismodal", 1)
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("403")
                ->renderViewOnly();
        }

        $this->addGlobalVar("ismodal", 1);
        try {
            $picklist = SF::getInstanceOf(PicklistService::class);
            $businessowners =  ($this->authService->hasAuthUserSystemProfile())
                ? $picklist->getUsersByProfile(UserProfileType::BUSINESS_OWNER)
                : [];

            $edit = SF::getInstanceOf(PromotionsInfoService::class, [$uuid]);
            $result = $edit->get_for_edit();
            $result["promotion"]["promotionlink"] = SF::getInstanceOf(BusinessSpaceService::class)->getPromotionUrlByPromotionUuid($uuid) ?? "";

            //dd($result);
            $this->setTemplateBySubPath("update")
                ->addGlobalVar(PageType::TITLE, __("Edit promotion {0}", $uuid))
                ->addGlobalVar(PageType::H1, __("Edit promotion {0}", $uuid))
                ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
                ->addGlobalVar("user", $this->authService->getAuthUserArray())
                ->addGlobalVar("uuid", $uuid)
                ->addGlobalVar("result", $result)
                ->addGlobalVar("timezones", $picklist->getTimezones())
                ->addGlobalVar("businessowners", $businessowners)
                ->addGlobalVar("notoryes", $picklist->getNotOrYesOptions())
                //->add_var("statistics", SF::get(PromotionsStatsService::class, ["uuid"=>$uuid])())
                ->addGlobalVar("statspermission", $this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTION_STATS_READ));

            unset($picklist, $businessowners, $edit, $result, $slug);
            $this->view->renderViewOnly();
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
            $this->logErr($e->getMessage(), "promotionupdate.controller");
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->addGlobalVar(PageType::H1, $e->getMessage())
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("500")
                ->renderViewOnly();
        }
    }//edit

    //@patch
    public function update(string $uuid): void
    {
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        if (!$this->csrfService->isValidCsrfToken($this->_getCsrfTokenFromRequest())) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ExceptionType::CODE_UNAUTHORIZED)
                ->setErrors([__("Invalid CSRF token")])
                ->show();
        }

        try {
            $request = ["uuid" => $uuid] + $this->requestComponent->getPost();
            $update = SF::getCallableService(PromotionsUpdateService::class, $request);
            $result = $update();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} {1} successfully updated", __("Promotion"), $uuid),
                "result" => $result,
            ])->show();
        } catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([["fields_validation" => $update->getErrors()]])
                ->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//update

}//PromotionsUpdateController
