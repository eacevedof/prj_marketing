<?php

namespace App\Restrict\Subscriptions\Infrastructure\Controllers;

use Exception;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{PageType, ResponseType, UrlType};
use App\Restrict\Subscriptions\Application\SubscriptionsSearchService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class SubscriptionsSearchController extends RestrictController
{
    public function index(?string $page = null): void
    {
        $this->_redirectToLoginIfNoAuthUser();
        try {
            $search = SF::getInstanceOf(SubscriptionsSearchService::class);

            $this->addGlobalVar(PageType::TITLE, __("Subscriptions"))
                ->addGlobalVar(PageType::H1, __("Subscriptions"))
                ->addGlobalVar("datatableHelper", $search->getDatatableHelper())
                ->addGlobalVar("idOwner", $this->authService->getIdOwner())
                ->addGlobalVar("authRead", $this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_READ))
                ->addGlobalVar("authWrite", $this->authService->hasAuthUserPolicy(UserPolicyType::SUBSCRIPTIONS_WRITE))
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->responseComponent->location(UrlType::ERROR_FORBIDDEN);
        }
        catch (Exception $e) {
            $this->logErr($e->getMessage(), "promotionscontroller.index");
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
            $search = SF::getCallableService(SubscriptionsSearchService::class, $this->requestComponent->getGet());
            $result = $search();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message"  => __("auth ok"),
                "result"   => $result["result"],
                "filtered" => $result["total"],
                "total"    => $result["total"],
                "req_uuid" => $result["req_uuid"],
            ])->show();
        }
        catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//search

}//SubscriptionsSearchController
