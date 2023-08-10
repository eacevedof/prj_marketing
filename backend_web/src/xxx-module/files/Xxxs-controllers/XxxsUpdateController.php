<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Xxxs\Infrastructure\Controllers\XxxsUpdateController
 * @file XxxsUpdateController.php v1.0.0
 * @date %DATE% SPAIN
 * @observations
 */

namespace App\Restrict\Xxxs\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{ExceptionType, PageType, ResponseType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Restrict\Xxxs\Application\{XxxsInfoService, XxxsUpdateService};
use App\Shared\Infrastructure\Exceptions\{FieldsException, ForbiddenException, NotFoundException};

final class XxxsUpdateController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
        $this->picklist = SF::getInstanceOf(PicklistService::class);
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
            $edit = SF::getInstanceOf(XxxsInfoService::class, [$uuid]);
            $result = $edit->get_for_edit();
            $this->setTemplateBySubPath("update")
                ->addGlobalVar(PageType::TITLE, __("Edit xxx {0}", $uuid))
                ->addGlobalVar(PageType::H1, __("Edit xxx {0}", $uuid))
                ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
                ->addGlobalVar("uuid", $uuid)
                ->addGlobalVar("result", $result)
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
            $update = SF::getCallableService(XxxsUpdateService::class, $request);
            $result = $update();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} {1} successfully updated", __("Xxx"), $uuid),
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

}//XxxsUpdateController
