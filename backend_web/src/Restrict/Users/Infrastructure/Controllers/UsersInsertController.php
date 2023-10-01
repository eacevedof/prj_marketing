<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersInsertController
 * @file UsersInsertController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Restrict\Users\Application\UsersInsertService;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class UsersInsertController extends RestrictController
{
    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
    }

    //@modal
    public function create(): void
    {
        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE)) {
            $this->addGlobalVar(PageType::TITLE, __("Unauthorized"))
                ->addGlobalVar(PageType::H1, __("Unauthorized"))
                ->addGlobalVar("ismodal", 1)
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("403")
                ->renderViewOnly();
        }

        $picklistService = SF::getInstanceOf(PicklistService::class);
        $this->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
            ->setPartViewName("insert")
            ->addGlobalVar(PageType::H1, __("New user"))
            ->addGlobalVar("profiles", $picklistService->getUserProfiles())
            ->addGlobalVar("parents", $picklistService->getUsersByProfile(UserProfileType::BUSINESS_OWNER))
            ->addGlobalVar("countries", $picklistService->getCountries())
            ->addGlobalVar("languages", $picklistService->getLanguages())
            ->renderViewOnly();
    }//create

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
            /**
             * @type UsersInsertService $usersInsertService
             */
            $usersInsertService = SF::getCallableService(UsersInsertService::class, $this->requestComponent->getPost());
            $userInserted = $usersInsertService();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully created", __("User")),
                "result" => $userInserted,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([
                    ["fields_validation" => $usersInsertService->getErrors()]
                ])
                ->show();
        }
        catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }

    }//insert

}//UsersInsertController
