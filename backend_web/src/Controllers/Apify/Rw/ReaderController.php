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

use TheFramework\Helpers\HelperJson;
use App\Controllers\Apify\ApifyController;
use App\Services\Apify\Rw\ReaderService;
use App\Factories\EncryptFactory;

final class ReaderController extends ApifyController
{
    /**
     * /apify/read?context=c&dbname=d
     */
    public function index(): void
    {
        $this->_check_usertoken();
        $idContext = $this->request->get_get("context");
        $sDbalias = $this->request->get_get("schemainfo");
        //$arParts = $this->request->get_post("queryparts");
        $arParts = EncryptFactory::get()->get_decrypted($this->request->get_post());
        
        $oServ = new ReaderService($idContext, $sDbalias);
        $arJson = $oServ->get_read($arParts);
        $iNumrows = $oServ->get_foundrows($arParts);
        $this->logd($iNumrows,"NUM_ROWS");

        if($oServ->is_error()) 
            $this->_get_json()->set_code(HelperJson::CODE_INTERNAL_SERVER_ERROR)->
                    set_error($oServ->get_errors())->
                    set_message("database error")->
                    show(1);

        $this->_get_json()->set_payload(["result"=>$arJson,"foundrows"=>$oServ->get_foundrows()])->show();

    }//index

    /**
     * /apify/read/raw?context=c&dbname=d
     */
    public function raw(): void
    {
        $this->_check_usertoken();
        $idContext = $this->request->get_get("context");
        $sDb = $this->request->get_get("dbname");

        $sSQL = $this->request->get_post("query");
        $oServ = new ReaderService($idContext,$sDb);

        $arJson = $oServ->read_raw($sSQL);
        if($oServ->is_error()) 
            $this->_get_json()->set_code(HelperJson::CODE_INTERNAL_SERVER_ERROR)->
                    set_error($oServ->get_errors())->
                    set_message("database error")->
                    show(1);

        $this->_get_json()->set_payload(["rows"=>$arJson,"numrows"=>$oServ->get_foundrows()])->show();
    }//raw
   

}//ReaderController
