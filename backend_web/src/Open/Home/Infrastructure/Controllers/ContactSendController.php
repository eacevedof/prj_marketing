<?php

namespace App\Open\Home\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Restrict\Auth\Application\CsrfService;
use App\Open\Home\Application\ContactSendService;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Open\PromotionCaps\Domain\Enums\RequestActionType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;

final class ContactSendController extends OpenController
{
    public function send(): void
    {
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        if (!SF::getInstanceOf(CsrfService::class)->isValidCsrfToken($this->_getCsrfTokenFromRequest())) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::FORBIDDEN)
                ->setErrors([__("Invalid CSRF token")])
                ->show();
        }

        $post = $this->requestComponent->getPost();
        if (($post["_action"] ?? "") !== RequestActionType::HOME_CONTACT_SEND) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Wrong action")])
                ->show();
        }

        $send = SF::getCallableService(ContactSendService::class, $post);
        try {
            $result = $send();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => $result["description"],
            ])->show();
        } catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([
                    ["fields_validation" => $send->getErrors()]
                ])
                ->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }
}
