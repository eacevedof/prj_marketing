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

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Users\Application\UserPreferencesListService;
use App\Shared\Domain\Enums\ResponseType;
use \Exception;

final class UsersPreferencesListController extends RestrictController
{
    //@get
    public function index(string $uuid): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $list = SF::get_callable(UserPreferencesListService::class, ["_useruuid"=>$uuid]);
            $result = $list();
            $this->_get_json()->set_payload([
                "result" => $result,
            ])->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }
}
