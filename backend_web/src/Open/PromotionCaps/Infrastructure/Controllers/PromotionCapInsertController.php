<?php
/**
 * @link eduardoaf.com
 */

namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use Exception;
use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Open\PromotionCaps\Domain\Enums\RequestActionType;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Open\PromotionCaps\Application\PromotionCapsInsertService;

final class PromotionCapInsertController extends OpenController
{
    public function insert(string $businessSlug, string $promotionUuid): void
    {
        if (!($businessSlug && $promotionUuid)) {
            $this->setLayoutBySubPath("open/mypromos/error")
                ->addHeaderCode($code = ResponseType::BAD_REQUEST)
                ->addGlobalVar(PageType::TITLE, $title = __("Subscription error!"))
                ->addGlobalVar(PageType::H1, $title)
                ->addGlobalVar("error", __("Missing partner and/or promotion"))
                ->addGlobalVar("code", $code)
                ->renderLayoutOnly();
        }

        if (!$promotionUuid) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("No promotion code provided")])
                ->show();
        }

        if (!$this->requestComponent->doClientAcceptJson()) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Only type json for accept header is allowed")])
                ->show();
        }

        $post = $this->requestComponent->getPost();
        if (($post["_action"] ?? "") !== RequestActionType::PROMOTIONCAP_INSERT) {
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors([__("Wrong action")])
                ->show();
        }

        try {
            $post = array_merge(["_businessslug" => $businessSlug, "_promotionuuid" => $promotionUuid], $post);
            /**
             * @var PromotionCapsInsertService $promotionCapsInsertService
             */
            $promotionCapsInsertService = SF::getCallableService(PromotionCapsInsertService::class, $post);

            $subscriptionCreated = $promotionCapsInsertService();
            $this->_getJsonInstanceFromResponse()->setPayload([
                "message" => $subscriptionCreated["description"],
                //"result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([
                    ["fields_validation" => $promotionCapsInsertService->getErrors()]
                ])
                ->show();
        }
        catch (Exception $e) {
            $this->_getJsonInstanceFromResponse()->setResponseCode($e->getCode())
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }
}
