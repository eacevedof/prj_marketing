<?php

namespace App\Restrict\Login\Infrastructure\Controllers;

use Exception;
use App\Restrict\Login\Application\LoginService;
use App\Restrict\Login\Application\Dtos\LoginDto;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Domain\Enums\{PageType, ResponseType, UrlType};
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class LoginController extends RestrictController
{
    public function index(): void
    {
        $this->addGlobalVar(PageType::TITLE, __("Login"))
            ->addGlobalVar(PageType::H1, __("Login"))
            ->addGlobalVar(PageType::CSRF, $this->csrfService->getCsrfToken())
            ->render();
    }

    //@post
    public function access(): void
    {
        if (!$this->csrfService->isValidCsrfToken($this->_getCsrfTokenFromRequest())) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::UNAUTHORIZED)
                ->setErrors([__("Invalid CSRF token")])
                ->show();
        }

        try {
            $loginDto = LoginDto::fromPrimitives([
                "email" => $this->requestComponent->getPost("email"),
                "password" => $this->requestComponent->getPost("password"),
            ]);
            $accessData = SF::getInstanceOf(LoginService::class)->get_access_or_fail($loginDto);
            $redirectUrl = $this->requestComponent->getRedirectUrl();
            $this->_getJsonInstanceFromResponse()
                ->setPayload([
                    "message" => __("auth ok"),
                    "lang" => $accessData["lang"],
                    UserPreferenceType::URL_DEFAULT_MODULE => $redirectUrl ?: $accessData[UserPreferenceType::URL_DEFAULT_MODULE]
                ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::UNAUTHORIZED)
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

    public function logout(): void
    {
        $this->_loadSessionComponentInstance();
        $this->sessionComponent->destroy();
        $this->responseComponent->location(UrlType::LOGIN_FORM);
    }

}//LoginController
