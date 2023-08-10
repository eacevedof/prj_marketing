<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersPermissionsUpdateController
 * @file UsersPermissionsUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Domain\Enums\{ExceptionType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Users\Application\UserPermissionsSaveService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Restrict\UserPermissions\Application\Dtos\UserPermissionsSaveDto;

final class UsersPermissionsUpdateController extends RestrictController
{
    //@put
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

        /**
         * @type UserPermissionsSaveService $userPermissionSaveService
         */
        $userPermissionSaveService = SF::getCallableService(UserPermissionsSaveService::class);
        try {
            $userPermissionSaveDto = UserPermissionsSaveDto::fromPrimitives([
                "userUuid" => $uuid,
                "id" => $this->requestComponent->getPost("id"),
                "uuid" => $this->requestComponent->getPost("uuid"),
                "idUser" => $this->requestComponent->getPost("id_user"),
                "jsonRw" => $this->requestComponent->getPost("json_rw"),
            ]);

            $result = $userPermissionSaveService($userPermissionSaveDto);
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully saved", __("User permission")),
                "result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([["fields_validation" => $userPermissionSaveService->getErrors()]])
                ->show();
        }
        catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

}//UsersPermissionsUpdateController
