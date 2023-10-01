<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersSearchController
 * @file UsersSearchController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Users\Application\UsersSearchService;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{PageType, ResponseType, UrlType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class UsersSearchController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::getInstanceOf(PicklistService::class);
    }

    public function index(?string $page = null): void
    {
        $this->_redirectToLoginIfNoAuthUser();
        try {
            $search = SF::getInstanceOf(UsersSearchService::class);

            $this->addGlobalVar(PageType::TITLE, __("Users"))
                ->addGlobalVar(PageType::H1, __("Users"))
                ->addGlobalVar("languages", $this->picklist->getLanguages())
                ->addGlobalVar("profiles", $this->picklist->getUserProfiles())
                ->addGlobalVar("countries", $this->picklist->getCountries())
                ->addGlobalVar("datatableHelper", $search->getDatatableHelper())
                ->addGlobalVar("authRead", $this->authService->hasAuthUserPolicy(UserPolicyType::USERS_READ))
                ->addGlobalVar("authWrite", $this->authService->hasAuthUserPolicy(UserPolicyType::USERS_WRITE))
                ->render();
        } catch (ForbiddenException $e) {
            $this->responseComponent->location(UrlType::ERROR_FORBIDDEN);
        } catch (Exception $e) {
            $this->logErr($e->getMessage(), "userscontroller.index");
            $this->responseComponent->location(UrlType::ERROR_INTERNAL);
        }

    }//index

    //@get
    public function search(): void
    {
        if (!$this->authService->getAuthUserArray()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::UNAUTHORIZED)
                ->setErrors([__("Your session has finished please re-login")])
                ->show();
        }

        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        try {
            $search = SF::getCallableService(UsersSearchService::class, $this->requestComponent->getGet());
            $result = $search();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message"  => __("auth ok"),
                "result"   => $result["result"],
                "filtered" => $result["total"],
                "total"    => $result["total"],
                "req_uuid" => $result["req_uuid"],
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//search

}//UsersSearchController
