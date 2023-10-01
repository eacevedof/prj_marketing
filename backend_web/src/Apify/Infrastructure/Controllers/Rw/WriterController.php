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

use App\Shared\Domain\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Apify\Application\Rw\WriterService;
use App\Shared\Infrastructure\Factories\ServiceFactory;

final class WriterController extends ApifyController
{
    /**
     * /apify/write/
     */
    public function index()
    {
        $this->_check_usertoken();
        $idcontext = $this->requestComponent->getGet("context");
        $dbalias = $this->requestComponent->getGet("schemainfo");
        //$arParts = $this->request->get_post("queryparts");
        $post = $this->requestComponent->getPost();
        $arParts = ServiceFactory::getInstanceOf("Apify/Encrypts")->get_decrypted($post);

        $action = $this->requestComponent->getPost("action");
        $arParts["useruuid"] = $this->requestComponent->getPost("useruuid");
        $table = $arParts["table"];

        $json = $this->_getJsonInstanceFromResponse();
        try {
            $oServ = new WriterService($idcontext, $dbalias, $table);
            $arJson = $oServ->setAction($action)->write($arParts);

            if ($oServ->isError()) {
                $json->setResponseCode()
                    ->setErrors($oServ->getErrors())
                    ->setMessage("database error")
                    ->show();
            }

            if ($action == "insert") {
                $json->setResponseCode(ResponseType::CREATED)->setMessage("resource created");
            } elseif ($action == "update") {
                $json->setMessage("resource updated");
            } elseif ($action == "delete") {
                $json->setMessage("resource deleted");
            } elseif ($action == "deletelogic") {
                $json->setMessage("resource deleted *");
            }

            $json->setPayload(["result" => $arJson, "lastid" => $oServ->getLastInsertId()])->show();
        } catch (\Exception $ex) {
            $this->logerr($ex->getMessage(), "writecontroller-exception");
            $json->setResponseCode(ResponseType::BAD_REQUEST)
                ->setErrors(["exception"])
                ->setMessage($ex->getMessage())
                ->show();
        }
    }//index

    /**
     * /apify/write/raw?context=c&dbname=d
     */
    public function raw()
    {
        $this->_check_usertoken();
        $idcontext = $this->requestComponent->getGet("context");
        $sDb = $this->requestComponent->getGet("dbname");
        $action = $this->requestComponent->getPost("action");
        $sSQL = $this->requestComponent->getPost("query");

        $oServ = new WriterService($idcontext, $sDb);
        $arJson = $oServ->executeWriteFromRawSql($sSQL);

        $json = $this->_getJsonInstanceFromResponse();
        if ($oServ->isError()) {
            $json->setResponseCode(ResponseType::INTERNAL_SERVER_ERROR)->
                    setErrors($oServ->getErrors())->
                    setMessage("database error")->
                    show(1);
        }

        if ($action == "insert") {
            $json->setResponseCode(ResponseType::CREATED)->setMessage("resource created");
        } elseif ($action == "update") {
            $json->setMessage("resource updated");
        } elseif ($action == "delete") {
            $json->setMessage("resource deleted");
        } elseif ($action == "deletelogic") {
            $json->setMessage("resource deleted *");
        }

        $json->setPayload($arJson)->show();
    }//raw

}//WriterController
