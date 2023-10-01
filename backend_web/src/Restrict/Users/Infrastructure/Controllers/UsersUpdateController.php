<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersUpdateController
 * @file UsersUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Application\Dtos\UserUpdateDto;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{ExceptionType, PageType, ResponseType};
use App\Restrict\Users\Domain\Enums\{UserPolicyType, UserProfileType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Restrict\Users\Application\{UsersInfoService, UsersUpdateService};
use App\Shared\Infrastructure\Exceptions\{FieldsException, ForbiddenException, NotFoundException};

final class UsersUpdateController extends RestrictController
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
        if (!$this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE)) {
            $this->addGlobalVar(PageType::TITLE, __("Unauthorized"))
                ->addGlobalVar(PageType::H1, __("Unauthorized"))
                ->addGlobalVar("ismodal", 1)
                ->setPartViewFolder("Open/Errors/Infrastructure/Views")
                ->setPartViewName("403")
                ->renderViewOnly();
        }

        $this->addGlobalVar("ismodal", 1);
        try {
            $result = SF::getInstanceOf(UsersInfoService::class, [$uuid])->getUsersInfoForEdition();
            $h1 = "{$result["user"]["description"]} ($uuid)";

            $this->setTemplateBySubPath("update")
                ->addGlobalVar(PageType::TITLE, __("Edit user {0}", $uuid))
                ->addGlobalVar(PageType::H1, __("Edit user {0}", $h1))
                ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
                ->addGlobalVar("uuid", $uuid)
                ->addGlobalVar("result", $result)

                ->addGlobalVar("profiles", $this->picklist->getUserProfiles())
                ->addGlobalVar("parents", $this->picklist->getUsersByProfile(UserProfileType::BUSINESS_OWNER))
                ->addGlobalVar("countries", $this->picklist->getCountries())
                ->addGlobalVar("languages", $this->picklist->getLanguages())
                ->addGlobalVar("timezones", $this->picklist->getTimezones())
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
    }//modal

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

        /**
         * @type UsersUpdateService $usersUpdateService
         */
        $usersUpdateService = SF::getSimpleInstanceOf(UsersUpdateService::class);
        try {
            $userUpdateDto = $this->getUserUpdateDto($uuid);
            $result = $usersUpdateService($userUpdateDto);
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully created", __("User")),
                "result" => $result,
            ])->show();
        } catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([["fields_validation" => $usersUpdateService->getErrors()]])
                ->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//patch

    private function getUserUpdateDto(string $userUUid): UserUpdateDto
    {
        return UserUpdateDto::fromPrimitives([
            "id" => $this->requestComponent->getPost("id"),
            "uuid" => $userUUid,//$this->requestComponent->getPost("uuid"),
            "address" => $this->requestComponent->getPost("address"),
            "birthdate" => $this->requestComponent->getPost("birthdate"),
            "email" => $this->requestComponent->getPost("email"),
            "fullname" => $this->requestComponent->getPost("fullname"),
            "idCountry" => $this->requestComponent->getPost("id_country"),
            "idLanguage" => $this->requestComponent->getPost("id_language"),
            "idParent" => $this->requestComponent->getPost("id_parent"),
            "idProfile" => $this->requestComponent->getPost("id_profile"),
            "secret" => $this->requestComponent->getPost("password"),
            "secret2" => $this->requestComponent->getPost("password2"),
            "phone" => $this->requestComponent->getPost("phone"),
        ]);
    }
}//UsersUpdateController
