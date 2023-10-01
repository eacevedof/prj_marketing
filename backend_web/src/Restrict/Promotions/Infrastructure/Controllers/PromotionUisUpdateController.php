<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 */

namespace App\Restrict\Promotions\Infrastructure\Controllers;

use Exception;
use App\Picklist\Application\PicklistService;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Domain\Enums\{ExceptionType, ResponseType};
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Promotions\Application\PromotionUiUpdateService;
use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;

final class PromotionUisUpdateController extends RestrictController
{
    private PicklistService $picklist;

    public function __construct()
    {
        parent::__construct();
        $this->_redirectToLoginIfNoAuthUser();
        $this->picklist = SF::getInstanceOf(PicklistService::class);
    }

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

        try {
            $request = ["_promotionuuid" => $uuid] + $this->requestComponent->getPost();
            $update = SF::getCallableService(PromotionUiUpdateService::class, $request);
            $result = $update();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => __("{0} {1} successfully updated", __("Promotion UI"), $uuid),
                "result" => $result,
            ])->show();
        } catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([["fields_validation" => $update->getErrors()]])
                ->show();
        } catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }//update

}//PromotionUisUpdateController
