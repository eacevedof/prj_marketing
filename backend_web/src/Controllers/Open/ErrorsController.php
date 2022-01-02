<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\ErrorsController
 * @file ErrorsController.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Controllers\Open;

use App\Components\Kafka\ProducerComponent;
use App\Enums\KeyType;
use App\Enums\ResponseType;


final class ErrorsController extends OpenController
{
    public function index(): void
    {
        $sPath = realpath(__DIR__."/../routes/routes.php");
        $arRutas = include $sPath;
        s("<pre>");
        foreach($arRutas as $arRuta)
            s("<a href=\"{$arRuta["url"]}\" target=\"_blank\">{$arRuta["url"]}</a><br/>");
    }

    public function error_404(): void
    {
        //(new ProducerComponent())->send(date("Y-m-d: H:i:s")." lalo","nada");
        $this->logerr($_SERVER["REQUEST_URI"],"error-404");
        $contenttype = $this->request->get_header("Accept");
        if (strstr($contenttype, "text/html"))
            $this->set_layout("error/error")
                ->set_template("error/404")
                ->add_var(KeyType::PAGE_TITLE, __("Content not found"))
                ->add_var("h1", __("Content not found"))
                ->add_var("urlback",$_SERVER["HTTP_REFERER"] ?? "/")
                ->render();
        else
            $this->_get_json()->set_code(ResponseType::NOT_FOUND)->set_error("Resource not found");
    }

    public function forbidden(): void
    {
        $this->set_layout("error/error")
            ->add_var(KeyType::PAGE_TITLE, __("Forbidden - 403"))
            ->add_var("h1", __("Unauthorized"))
        ;

        $this->render([],"error/403");
    }

}//ErrorsController
