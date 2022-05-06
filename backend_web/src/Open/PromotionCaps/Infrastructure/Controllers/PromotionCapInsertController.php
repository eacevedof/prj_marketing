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
use App\Open\PromotionCaps\Application\PromotionCapsInsertService;

use App\Open\PromotionCaps\Domain\Enums\RequestActionType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Infrastructure\Exceptions\FieldsException;

final class PromotionCapInsertController extends OpenController
{
    public function insert(string $promouuid): void
    {
        if (!$promouuid)
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("No promotion code provided")])
                ->show();

        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        $post = $this->request->get_post();
        if (($post["_action"] ?? "") !== RequestActionType::PROMOTIONCAP_INSERT)
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Wrong action")])
                ->show();

        $post = ["_promotionuuid"=>$promouuid] + $post;
        $insert = SF::get_callable(PromotionCapsInsertService::class, $post);
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



