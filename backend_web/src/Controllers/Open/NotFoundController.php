<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\NotFoundController 
 * @file NotFoundController.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Controllers\Open;

use App\Components\Kafka\ProducerComponent;
use App\Enums\ResponseType;


final class NotFoundController extends OpenController
{

    public function index()
    {
        $sPath = realpath(__DIR__."/../routes/routes.php");
        $arRutas = include $sPath;
        s("<pre>");
        foreach($arRutas as $arRuta)
            s("<a href=\"{$arRuta["url"]}\" target=\"_blank\">{$arRuta["url"]}</a><br/>");
    }

    public function error_404()
    {
        (new ProducerComponent())->send(date("Y-m-d: H:i:s")." lalo","nada");
        $this->logerr($_SERVER["REQUEST_URI"],"error-404");
        $this->_get_json()->set_code(ResponseType::NOT_FOUND)->set_error("Resource not found");
    }    

}//NotFoundController
