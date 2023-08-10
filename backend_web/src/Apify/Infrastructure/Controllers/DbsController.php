<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\DbsController
 * @file DbsController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */

namespace App\Controllers\Apify;

use TheFramework\Helpers\HelperJson;
use App\Services\Apify\{ContextService, DbsService};

final class DbsController extends ApifyController
{
    public function __construct()
    {
        //captura trazas de la petición en los logs
        parent::__construct();
    }

    /**
     * ruta:    <dominio>/apify/dbs/{id_context}
     * Muestra los schemas
     */
    public function index()
    {
        $oJson = new HelperJson;
        $idContext = $this->requestComponent->getGet("id_context");

        $oServ = new ContextService;
        if (!$oServ->is_context($idContext)) {
            $oJson->setResponseCode(HelperJson::CODE_NOT_FOUND)
                ->setErrors("context does not exist")
                ->show();
        }

        $oServ = new DbsService($idContext);
        $arJson = $oServ->get_all();

        if ($oServ->is_error()) {
            $oJson->setResponseCode(HelperJson::CODE_INTERNAL_SERVER_ERROR)
                ->setErrors($oServ->getErrors())
                ->setMessage("database error")
                ->show();
        }

        $oJson->setPayload($arJson)->show();
    }//index

}//DbsController
