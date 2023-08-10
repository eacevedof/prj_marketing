<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersDeleteController
 * @file UsersDeleteController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Application\UsersDeleteService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class UsersDeleteController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
        $this->picklist = SF::getInstanceOf(PicklistService::class);
    }

    //@delete
    public function remove(string $uuid): void
    {
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        try {
            $delete = SF::getCallableService(UsersDeleteService::class, ["uuid" => $uuid]);
            $result = $delete();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully removed", __("User")),
                "result" => $result,
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//remove

    //@undelete
    public function undelete(string $uuid): void
    {
        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }
        try {
            $delete = SF::getInstanceOf(UsersDeleteService::class, ["uuid" => $uuid]);
            $result = $delete->undelete();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully restored", __("User")),
                "result" => $result,
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//undelete

}//UsersDeleteController
