<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInsertController
 * @file PromotionsInsertController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Promotions\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Promotions\Application\PromotionsInsertService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class PromotionsInsertController extends RestrictController
{
    //@modal (creation form)
    public function create(): void
    {
        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::PROMOTIONS_WRITE)) {
            $this->addGlobalVar(PageType::TITLE, __("Unauthorized"))
                ->addGlobalVar(PageType::H1, __("Unauthorized"))
                ->addGlobalVar("ismodal", 1)
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("403")
                ->renderViewOnly();
        }

        $picklist = SF::getInstanceOf(PicklistService::class);
        //to-do business owners que tengan businessdata
        $businessowners =  ($this->authService->isAuthUserRoot() || $this->authService->isAuthUserSysadmin())
            ? $picklist->getBusinessOwners()
            : [];

        $this->setTemplateBySubPath("insert")
            ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
            ->addGlobalVar(PageType::H1, __("New promotion"))
            ->addGlobalVar("timezones", $picklist->getTimezones())
            ->addGlobalVar("businessowners", $businessowners)
            ->addGlobalVar("notoryes", $picklist->getNotOrYesOptions())
            ->renderViewOnly();
    }

    //@post
    public function insert(): void
    {
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        if (!$this->csrfService->isValidCsrfToken($this->_getCsrfTokenFromRequest())) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::FORBIDDEN)
                ->setErrors([__("Invalid CSRF token")])
                ->show();
        }

        try {
            $insert = SF::getCallableService(PromotionsInsertService::class, $this->requestComponent->getPost());
            $result = $insert();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully created", "Promotion"),
                "result" => $result,
            ])->show();
        } catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([
                    ["fields_validation" => $insert->getErrors()]
                ])
                ->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }

    }//insert

}//PromotionsInsertController
