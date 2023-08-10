<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\RestrictController
 * @file RestrictController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Controllers\Restrict;

use App\Restrict\Login\Application\ModulesService;
use App\Shared\Infrastructure\Controllers\AppController;
use App\Restrict\Auth\Application\{AuthService, CsrfService};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Helpers\RoutesHelper as Routes;
use App\Restrict\BusinessData\Application\BusinessDataDisabledService;
use App\Shared\Infrastructure\Traits\{RequestTrait, ResponseTrait, SessionTrait, ViewTrait};

abstract class RestrictController extends AppController
{
    use RequestTrait;
    use ResponseTrait;
    use SessionTrait;
    use ViewTrait;

    protected AuthService $authService;
    protected CsrfService $csrfService;

    /**
     * Builds request, response, auth, csrf, restrict-layout and toppmenu
     * add authuser to views
     */
    public function __construct()
    {
        $this->_loadRequestComponentInstance();
        $this->_loadResponseComponentInstance();

        $this->authService = SF::getAuthService();
        $this->csrfService = SF::getInstanceOf(CsrfService::class);

        $this->_loadViewInstance()->setPartLayout("restrict/restrict");
        $this->addGlobalVar("authUser", $this->authService->getAuthUserArray());
        $this->_addTopModulesMenu();
        $this->_addBusinessOwnerDisabled();
    }

    protected function _addTopModulesMenu(): void
    {
        /**
         * @type ModulesService $service
         */
        $service = SF::getCallableService(ModulesService::class);
        $this->addGlobalVar("topmenu", $service->getMenuConfiguration());
    }

    /**
     * Works only after __construct() execution
     */
    protected function _redirectToLoginIfNoAuthUser(): void
    {
        if ($this->authService->getAuthUserArray()) {
            return;
        }

        $redirect = $this->requestComponent->getRequestUri();
        $loginUrl = Routes::getUrlByRouteName("login");
        if (strstr($redirect, "/restrict")) {
            $loginUrl = "$loginUrl?redirect=".urlencode($redirect);
        }
        $this->responseComponent->location($loginUrl);
    }

    protected function _addBusinessOwnerDisabled(): void
    {
        $this->addGlobalVar("bowdisabled", []);
        if (!($idUser = $this->authService->getAuthUserArray()["id"] ?? "")) {
            return;
        }
        if ($this->authService->hasAuthUserSystemProfile()) {
            return;
        }

        /**
         * @type BusinessDataDisabledService $service
         */
        $service = SF::getInstanceOf(BusinessDataDisabledService::class);
        $this->addGlobalVar(
            "bowdisabled",
            $service->getDisabledDataByUser($this->authService->getIdOwner())
        );
    }

}//RestrictController
