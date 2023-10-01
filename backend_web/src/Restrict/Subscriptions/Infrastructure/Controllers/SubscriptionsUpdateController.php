<?php

namespace App\Restrict\Subscriptions\Infrastructure\Controllers;

use Exception;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{ExceptionType, PageType, ResponseType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Restrict\Subscriptions\Application\{SubscriptionsInfoService, SubscriptionsUpdateService};
use App\Shared\Infrastructure\Exceptions\{FieldsException, ForbiddenException, NotFoundException};

final class SubscriptionsUpdateController extends RestrictController
{
    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
    }

    //@modal
    public function edit(string $uuid): void
    {
        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_WRITE)) {
            $this->addGlobalVar(PageType::TITLE, __("Unauthorized"))
                ->addGlobalVar(PageType::H1, __("Unauthorized"))
                ->addGlobalVar("ismodal", 1)
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("403")
                ->renderViewOnly();
        }

        $this->addGlobalVar("ismodal", 1);
        try {
            $edit = SF::getInstanceOf(SubscriptionsInfoService::class, [$uuid]);
            $result = $edit->get_info_for_execute_date();

            $this->setTemplateBySubPath("update-status")
                ->addGlobalVar(PageType::TITLE, __("Edit subscription {0}", $uuid))
                ->addGlobalVar(PageType::H1, __("Edit subscription {0}", $uuid))
                ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
                ->addGlobalVar("user", $this->authService->getAuthUserArray())
                ->addGlobalVar("uuid", $uuid)
                ->addGlobalVar("result", $result);
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
            $this->logErr($e->getMessage(), "subscriptionupdate.controller");
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->addGlobalVar(PageType::H1, $e->getMessage())
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("500")
                ->renderViewOnly();
        }
    }//edit

    //@patch
    public function update_status(string $uuid): void
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
            $update = SF::getCallableService(SubscriptionsUpdateService::class, $request);
            $result = $update();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} {1} successfully updated", __("Subscription"), $uuid),
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

}//SubscriptionsUpdateController
