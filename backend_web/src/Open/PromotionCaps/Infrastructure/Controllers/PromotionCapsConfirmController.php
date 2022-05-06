<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PromotionCaps\Application\PromotionCapsConfirmService;

use App\Open\PromotionCaps\Domain\Enums\RequestActionType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class PromotionCapsConfirmController extends OpenController
{
    public function confirm(string $promouuid, string $subsuuid): void
    {
        if (!($subsuuid && $promouuid))
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("No {0} code provided", __("subscription"))])
                ->show();

        $insert = SF::get_callable(PromotionCapsConfirmService::class, ["uuid"=>$subsuuid]);
        try {
            $result = $insert();
            $this->_get_json()->set_payload([
                "message" => $result["description"],
                //"result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([
                    ["fields_validation" => $insert->get_errors()]
                ])
                ->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }
}



