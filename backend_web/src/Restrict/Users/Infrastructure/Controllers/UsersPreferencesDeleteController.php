<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersPreferencesDeleteController
 * @file UsersPreferencesDeleteController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Users\Application\UserPreferencesDeleteService;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use \Exception;

final class UsersPreferencesDeleteController extends RestrictController
{
    //@delete
    public function delete(string $uuid): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        if (!$this->csrf->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();

        try {
            $request = ["_useruuid"=>$uuid] + $this->request->get_post();
            $delete = SF::get_callable(UserPreferencesDeleteService::class, $request);
            $result = $delete();
            $this->_get_json()->set_payload([
                "message"=> __("{0} successfully deleted", __("User preference")),
                "result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([["fields_validation" => $delete->get_errors()]])
                ->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }//delete

}//UsersPreferencesDeleteController
