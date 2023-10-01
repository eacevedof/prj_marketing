<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\Xxxs\Infrastructure\Controllers\XxxsSearchController
 * @file XxxsSearchController.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\Xxxs\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Restrict\Xxxs\Application\XxxsSearchService;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{PageType, ResponseType, UrlType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class XxxsSearchController extends RestrictController
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
            $search = SF::getInstanceOf(XxxsSearchService::class);

            $this->addGlobalVar(PageType::TITLE, __("Xxxs"))
                ->addGlobalVar(PageType::H1, __("Xxxs"))
                ->addGlobalVar("datatableHelper", $search->getDatatableHelper())
                ->addGlobalVar("idOwner", $this->authService->getIdOwner())
                ->addGlobalVar("authRead", $this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_READ))
                ->addGlobalVar("authWrite", $this->authService->hasAuthUserPolicy(UserPolicyType::XXXS_WRITE))
                ->render();
        } catch (ForbiddenException $e) {
            $this->responseComponent->location(UrlType::ERROR_FORBIDDEN);
        } catch (Exception $e) {
            $this->logErr($e->getMessage(), "xxxscontroller.index");
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
            $search = SF::getCallableService(XxxsSearchService::class, $this->requestComponent->getGet());
            $result = $search();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message"  => __("auth ok"),
                "result"   => $result["result"],
                "filtered" => $result["total"],
                "total"    => $result["total"],
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//search

}//XxxsSearchController
