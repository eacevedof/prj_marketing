<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\ContextsController
 * @file ContextsController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */

namespace App\Controllers\Apify;

use App\Controllers\ApifyController;
use TheFramework\Helpers\HelperJson;
use App\Services\Apify\ContextService;

final class ContextsController extends ApifyController
{
    public function __construct()
    {
        //captura trazas de la petición en los logs
        parent::__construct();
    }

    /**
     * ruta:
     *  <dominio>/apify/contexts
     *  <dominio>/apify/contexts/{id}
     */
    public function index()
    {
        $oServ = new ContextService;

        $idContext = $this->request->get_get("id");
        $oJson = new HelperJson;
        if ($idContext) {
            //pr($oServ->is_context($idContext));die;
            //pr("con");die;
            if (!$oServ->is_context($idContext)) {
                $oJson->setResponseCode(HelperJson::CODE_NOT_FOUND)
                    ->setErrors("context does not exist")
                    ->show();
            }

            $arJson = $oServ->get_pubconfig_by_id($this->request->get_get("id"));
        } else {
            //pr("no id");
            $arJson = $oServ->get_pubconfig();
        }

        if ($oServ->isError()) {
            $oJson->setResponseCode(HelperJson::CODE_INTERNAL_SERVER_ERROR)
                ->setErrors($oServ->getErrors())
                ->setMessage("database error")
                ->show();
        }

        $oJson->setPayload($arJson)->show();

    }//index

}//ContextsController
