<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\Users\UsersDeleteController
 * @file UsersDeleteController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict\Users;

use App\Controllers\Restrict\RestrictController;
use App\Factories\ServiceFactory as SF;
use App\Services\Common\PicklistService;
use App\Enums\ResponseType;
use \Exception;

final class UsersDeleteController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get("Common\Picklist");
    }

    //@delete
    public function remove(string $uuid): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $delete = SF::get_callable("Restrict\Users\UsersDelete", ["uuid"=>$uuid]);
            $result = $delete();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully removed"),
                "result" => $result,
            ])->show();
        }
        catch (Exception $e)
        {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }//remove

    //@undelete
    public function undelete(string $uuid): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();
        try {
            $delete = SF::get_callable("Restrict\Users\UsersDelete", ["uuid"=>$uuid]);
            $result = $delete->undelete();
            $this->_get_json()->set_payload([
                "message"=>__("User successfully restored"),
                "result" => $result,
            ])->show();
        }
        catch (Exception $e)
        {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }//undelete

}//UsersDeleteController
