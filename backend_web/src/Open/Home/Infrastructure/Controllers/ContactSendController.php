<?php
namespace App\Open\Home\Infrastructure\Controllers;

use App\Open\PromotionCaps\Domain\Enums\RequestActionType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\CsrfService;
use App\Open\Home\Application\ContactSendService;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Shared\Domain\Enums\ResponseType;

final class ContactSendController extends OpenController
{
    public function send(): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        if (!SF::get(CsrfService::class)->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ResponseType::FORBIDDEN)
                ->set_error([__("Invalid CSRF token")])
                ->show();

        $post = $this->request->get_post();
        if (($post["_action"] ?? "") !== RequestActionType::HOME_CONTACT_SEND)
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Wrong action")])
                ->show();

        $send = SF::get_callable(ContactSendService::class, $post);
        try {
            $result = $send();
            $this->_get_json()->set_payload([
                "message" => $result["description"],
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([
                    ["fields_validation" => $send->get_errors()]
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



