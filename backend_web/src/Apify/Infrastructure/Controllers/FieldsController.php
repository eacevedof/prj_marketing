<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\FieldsController
 * @file FieldsController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */

namespace App\Controllers\Apify;

use TheFramework\Helpers\HelperJson;
use App\Services\Apify\{ContextService, FieldsService};

final class FieldsController extends ApifyController
{
    public function __construct()
    {
        //captura trazas de la petición en los logs
        parent::__construct();
        $this->_check_usertoken();
    }

    private function _get_database()
    {
        $oServ = new ContextService;
        $oJson = $this->_getJsonInstanceFromResponse();

        $idContext = $this->requestComponent->getGet("id_context");
        if (!$oServ->is_context($idContext)) {
            $oJson->setResponseCode(HelperJson::CODE_NOT_FOUND)
                ->setErrors("context does not exist")
                ->show();
        }

        //schemainfo puede ser un alias o un dbname
        $sDb = $this->requestComponent->getGet("schemainfo");

        //compruebo que sea una dbname
        $sDb2 = $oServ->is_db($idContext, $sDb);
        if ($sDb2) {
            return $sDb2;
        }

        //aqui $sdb se trata como dbalias
        $sDb2 = $oServ->get_db($idContext, $sDb);
        if ($sDb2) {
            return $sDb2;
        }

        $oJson->setResponseCode(HelperJson::CODE_NOT_FOUND)
            ->setErrors("no database found in context 1")
            ->show();
    }

    /**
     * /apify/fields/{id_context}/{schemainfo}/{tablename}/{fieldname}
     * /apify/fields/{id_context}/{schemainfo}/{tablename}
     * Muestra los schemas
     */
    public function index()
    {
        //si no hay db lanza un exit
        $sDb = $this->_get_database();

        $idContext = $this->requestComponent->getGet("id_context");
        $sTableName = $this->requestComponent->getGet("tablename");
        $sFieldName = $this->requestComponent->getGet("fieldname");

        $oServ = new FieldsService($idContext, $sDb, $sTableName, $sFieldName);
        if ($sFieldName) {
            $arJson = $oServ->get_field($sTableName, $sFieldName);
        } else {
            $arJson = $oServ->get_all($sTableName);
        }

        $oJson = $this->_getJsonInstanceFromResponse();
        if ($oServ->isError()) {
            $oJson->setResponseCode(HelperJson::CODE_INTERNAL_SERVER_ERROR)
                    ->setErrors($oServ->getErrors())
                    ->setMessage("database error")
                    ->show();
        }

        $oJson->setPayload($arJson)->show();
    }//index

}//FieldsController
