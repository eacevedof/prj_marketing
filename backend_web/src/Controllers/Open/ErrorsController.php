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
use App\Enums\SessionType;
use App\Enums\ResponseType;


final class ErrorsController extends OpenController
{
    public function index(): void
    {
        $pathroute = realpath(__DIR__."/../routes/routes.php");
        $routes = include $pathroute;
        s("<pre>");
        foreach($routes as $route)
            s("<a href=\"{$route["url"]}\" target=\"_blank\">{$route["url"]}</a><br/>");
    }

    public function error_404(): void
    {
        if ($this->request->is_json())
            $this->_get_json()->set_code(ResponseType::NOT_FOUND)->set_error(__("Content not found"));
        else
            $this->set_layout("error/error")
                ->set_template("error/404")
                ->add_var(SessionType::PAGE_TITLE, __("Content not found"))
                ->add_var("h1", __("Content not found"))
                ->add_var("urlback",$this->request->get_referer() ?? "/")
                ->render();
    }

    public function forbidden(): void
    {
        if ($this->request->is_json())
            $this->_get_json()->set_code(ResponseType::FORBIDDEN)->set_error(__("Forbidden"));
        else
            $this->set_layout("error/error")
                ->set_template("error/403")
                ->add_var(SessionType::PAGE_TITLE, __("Forbidden"))
                ->add_var("h1", __("Forbidden"))
                ->add_var("urlback",$this->request->get_referer() ?? "/")
                ->render();
    }

}//ErrorsController
