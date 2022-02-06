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

use App\Shared\Domain\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Apify\Application\Rw\ReaderService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;

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
        
        $servreader = new ReaderService($idcontext, $dbalias);
        $json = $servreader->get_read($ardecrypted);
        if($servreader->is_error()) 
            $this->_get_json()->set_code(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_error($servreader->get_errors())
                ->set_message("database error")
                ->show();

        $this->_get_json()->set_payload(["result"=>$json, "foundrows"=>$servreader->get_foundrows()])->show();

    }//index

    /**
     * /apify/read/raw?context=c&dbname=d
     */
    public function raw(): void
    {
        $this->_check_usertoken();
        $idcontext = $this->request->get_get("context");
        $dbname = $this->request->get_get("dbname");

        $sql = $this->request->get_post("query");
        $servreader = new ReaderService($idcontext, $dbname);

        $json = $servreader->read_raw($sql);
        if($servreader->is_error()) 
            $this->_get_json()->set_code(ResponseType::INTERNAL_SERVER_ERROR)->
                    set_error($servreader->get_errors())->
                    set_message("database error")->
                    show();

        $this->_get_json()->set_payload(["rows"=>$json, "numrows"=>$servreader->get_foundrows()])->show();
    }//raw
   

}//ReaderController
