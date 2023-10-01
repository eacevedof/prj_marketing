<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersBusinessDataUpdateController
 * @file UsersBusinessDataUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Domain\Enums\{ExceptionType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Restrict\Users\Application\UserBusinessAttributeSpaceSaveService;

final class UsersBusinessAttributeSpaceUpdateController extends RestrictController
{
    //@patch
    public function update(string $uuid): void
    {
        $this->_redirectToLoginIfNoAuthUser();

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
            $request = ["_useruuid" => $uuid] + $this->requestComponent->getPost();
            $update = SF::getCallableService(UserBusinessAttributeSpaceSaveService::class, $request);
            $result = $update();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} {1} successfully updated", __("Space data"), $uuid),
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
    }
}
