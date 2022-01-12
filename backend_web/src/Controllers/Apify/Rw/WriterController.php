<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\Rw\WriterController 
 * @file WriterController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Apify\Rw;

use App\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Services\Apify\Rw\WriterService;
use App\Factories\ServiceFactory;

final class WriterController extends ApifyController
{

    /**
     * /apify/write/
     */
    public function index()
    {
        $this->_check_usertoken();
        $idcontext = $this->request->get_get("context");
        $dbalias = $this->request->get_get("schemainfo");
        //$arParts = $this->request->get_post("queryparts");
        $post = $this->request->get_post();
        $arParts = ServiceFactory::get("Apify/Encrypts")->get_decrypted($post);

        $action = $this->request->get_post("action");
        $arParts["useruuid"] = $this->request->get_post("useruuid");
        $table = $arParts["table"];
        
        $json = $this->_get_json();
        try 
        {
            $oServ = new WriterService($idcontext, $dbalias, $table);
            $arJson = $oServ->set_action($action)->write($arParts);

            if ($oServ->is_error())
                $json->set_code()
                    ->set_error($oServ->get_errors())
                    ->set_message("database error")
                    ->show();

            if ($action == "insert")
                $json->set_code(ResponseType::CREATED)->set_message("resource created");
            elseif ($action == "update")
                $json->set_message("resource updated");
            elseif ($action == "delete")
                $json->set_message("resource deleted");
            elseif ($action == "deletelogic")
                $json->set_message("resource deleted *");

            $json->set_payload(["result" => $arJson, "lastid" => $oServ->get_lastinsert_id()])->show();
        }
        catch (\Exception $ex)
        {
            $this->logerr($ex->getMessage(),"writecontroller-exception");
            $json->set_code(ResponseType::BAD_REQUEST)
                ->set_error(["exception"])
                ->set_message($ex->getMessage())
                ->show();
        }
    }//index

    /**
     * /apify/write/raw?context=c&dbname=d
     */
    public function raw()
    {
        $this->_check_usertoken();
        $idcontext = $this->request->get_get("context");
        $sDb = $this->request->get_get("dbname");
        $action = $this->request->get_post("action");
        $sSQL = $this->request->get_post("query");
        
        $oServ = new WriterService($idcontext,$sDb);
        $arJson = $oServ->write_raw($sSQL);

        $json = $this->_get_json();
        if($oServ->is_error()) 
            $json->set_code(ResponseType::INTERNAL_SERVER_ERROR)->
                    set_error($oServ->get_errors())->
                    set_message("database error")->
                    show(1);

        if($action=="insert") 
            $json->set_code(ResponseType::CREATED)->set_message("resource created");
        elseif($action=="update")
            $json->set_message("resource updated");
        elseif($action=="delete")
            $json->set_message("resource deleted");
        elseif($action=="deletelogic")
            $json->set_message("resource deleted *");

        $json->set_payload($arJson)->show();
    }//raw    
    
}//WriterController
