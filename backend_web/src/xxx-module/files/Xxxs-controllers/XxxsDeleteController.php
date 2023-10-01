<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\Xxxs\Infrastructure\Controllers\XxxsDeleteController
 * @file XxxsDeleteController.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\Xxxs\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Picklist\Application\PicklistService;
use App\Restrict\Xxxs\Application\XxxsDeleteService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class XxxsDeleteController extends RestrictController
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
            $delete = SF::getCallableService(XxxsDeleteService::class, ["uuid" => $uuid]);
            $result = $delete();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully removed", __("Xxx")),
                "result" => $result,
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

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
            $delete = SF::getCallableService(XxxsDeleteService::class, ["uuid" => $uuid]);
            $result = $delete->undelete();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully restored", __("Xxx")),
                "result" => $result,
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

}//XxxsDeleteController
