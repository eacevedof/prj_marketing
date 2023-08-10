<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsDeleteController
 * @file PromotionsDeleteController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */

namespace App\Restrict\Promotions\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Picklist\Application\PicklistService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Promotions\Application\PromotionsDeleteService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class PromotionsDeleteController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
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
            $delete = SF::getCallableService(PromotionsDeleteService::class, ["uuid" => $uuid]);
            $result = $delete();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully removed", __("Promotion")),
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
            $delete = SF::getCallableService(PromotionsDeleteService::class, ["uuid" => $uuid]);
            $result = $delete->undelete();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} successfully restored", __("Promotion")),
                "result" => $result,
            ])->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

}//PromotionsDeleteController
