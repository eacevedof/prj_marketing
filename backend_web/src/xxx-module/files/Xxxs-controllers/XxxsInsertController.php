<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Xxxs\Infrastructure\Controllers\XxxsInsertController
 * @file XxxsInsertController.php v1.0.0
 * @date %DATE% SPAIN
 * @observations
 */

namespace App\Restrict\Xxxs\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Xxxs\Application\XxxsInsertService;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class XxxsInsertController extends RestrictController
{
    private PicklistService $picklistService;

    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
        $this->picklistService = SF::getInstanceOf(PicklistService::class);
    }

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

        $businessowners =  ($this->authService->isAuthUserRoot() || $this->authService->isAuthUserSysadmin())
            ? $this->picklistService->getUsersByProfile(UserProfileType::BUSINESS_OWNER)
            : [];

        $this->setTemplateBySubPath("insert")
            ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
            ->addGlobalVar(PageType::H1, __("New xxx"))
            ->addGlobalVar("xxxs", $this->picklistService->get_xxx_types())
            ->addGlobalVar("businessowners", $businessowners)
            ->addGlobalVar("notoryes", $this->picklistService->getNotOrYesOptions())
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
            $insert = SF::getCallableService(XxxsInsertService::class, $this->requestComponent->getPost());
            $result = $insert();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully created", __("Xxx")),
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

}//XxxsInsertController
