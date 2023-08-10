<?php

namespace App\Restrict\Subscriptions\Infrastructure\Controllers;

use Exception;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Subscriptions\Application\SubscriptionsUpdateService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class SubscriptionsValidateQRController extends RestrictController
{
    //@modal
    public function edit(): void
    {
        $this->_redirectToLoginIfNoAuthUser();

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
            $this->setTemplateBySubPath("qr-update-status")
                ->addGlobalVar(PageType::TITLE, __("Validate QR subscription"))
                ->addGlobalVar(PageType::H1, __("Validate QR subscription"));
            $this->view->renderViewOnly();
        } catch (Exception $e) {
            $this->logErr($e->getMessage(), "subscriptionupdate.controller");
            $this->addHeaderCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->addGlobalVar(PageType::H1, $e->getMessage())
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("500")
                ->renderViewOnly();
        }
    }//edit

    //@put
    public function update_status(): void
    {
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        try {
            $request = [
                "uuid" => $uuid = $this->requestComponent->getPost("uuid", ""),
                "exec_code" => $this->requestComponent->getPost("exec_code", ""),
                "notes" => $this->requestComponent->getPost("notes", ""),
            ];
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
