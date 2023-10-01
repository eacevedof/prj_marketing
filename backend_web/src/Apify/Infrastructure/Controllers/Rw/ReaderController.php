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
        $idContext = $this->requestComponent->getGet("context");
        $dbAlias = $this->requestComponent->getGet("schemainfo");
        $queryConfig = SF::getInstanceOf("Apify/Encrypts")->get_decrypted($this->requestComponent->getPost());

        $readerService = new ReaderService($idContext, $dbAlias);
        $json = $readerService->execSqlByConfig($queryConfig);
        if ($readerService->isError()) {
            $this->_getJsonInstanceFromResponse()->setResponseCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setErrors($readerService->getErrors())
                ->setMessage("database error")
                ->show();
        }

        $this->_getJsonInstanceFromResponse()
            ->setPayload([
                "result" => $json,
                "foundrows" => $readerService->getFoundRows()
            ])
            ->show();

    }//index

    /**
     * /apify/read/raw?context=c&dbname=d
     */
    public function raw(): void
    {
        $this->_check_usertoken();
        $idcontext = $this->requestComponent->getGet("context");
        $dbname = $this->requestComponent->getGet("dbname");

        $sql = $this->requestComponent->getPost("query");
        $servreader = new ReaderService($idcontext, $dbname);

        $json = $servreader->execRawSql($sql);
        if ($servreader->isError()) {
            $this->_getJsonInstanceFromResponse()->setResponseCode(ResponseType::INTERNAL_SERVER_ERROR)->
                    setErrors($servreader->getErrors())->
                    setMessage("database error")->
                    show();
        }

        $this->_getJsonInstanceFromResponse()->setPayload(["rows" => $json, "numrows" => $servreader->getFoundRows()])->show();
    }//raw


}//ReaderController
