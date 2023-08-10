<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesListController
 * @file UsersPreferencesListController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Users\Application\UserPreferencesListService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class UsersPreferencesListController extends RestrictController
{
    //@get
    public function index(string $uuid): void
    {
        $this->_redirectToLoginIfNoAuthUser();
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        try {
            $list = SF::getCallableService(UserPreferencesListService::class, ["_useruuid" => $uuid]);
            $result = $list();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "result" => $result,
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }
}
