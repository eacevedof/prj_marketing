<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\Rw\ReaderController 
 * @file ReaderController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Apify\Rw;

use App\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Services\Apify\Rw\ReaderService;
use App\Factories\ServiceFactory as SF;

final class ReaderController extends ApifyController
{
    /**
     * /apify/read?context=c&dbname=d
     */
    public function index(): void
    {
        $this->_check_usertoken();
        $idcontext = $this->request->get_get("context");
        $dbalias = $this->request->get_get("schemainfo");
        $ardecrypted = SF::get("Apify/Encrypts")->get_decrypted($this->request->get_post());
        
        $oServ = new ReaderService($idcontext, $dbalias);
        $arJson = $oServ->get_read($ardecrypted);
        if($oServ->is_error()) 
            $this->_get_json()->set_code(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_error($oServ->get_errors())
                ->set_message("database error")
                ->show();

        $this->_get_json()->set_payload(["result"=>$arJson,"foundrows"=>$oServ->get_foundrows()])->show();

    }//index

    /**
     * /apify/read/raw?context=c&dbname=d
     */
    public function raw(): void
    {
        $this->_check_usertoken();
        $idcontext = $this->request->get_get("context");
        $sDb = $this->request->get_get("dbname");

        $sSQL = $this->request->get_post("query");
        $oServ = new ReaderService($idcontext,$sDb);

        $arJson = $oServ->read_raw($sSQL);
        if($oServ->is_error()) 
            $this->_get_json()->set_code(ResponseType::INTERNAL_SERVER_ERROR)->
                    set_error($oServ->get_errors())->
                    set_message("database error")->
                    show();

        $this->_get_json()->set_payload(["rows"=>$arJson,"numrows"=>$oServ->get_foundrows()])->show();
    }//raw
   

}//ReaderController
