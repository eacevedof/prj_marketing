<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Open\Errors\Infrastructure\Controllers\ErrorsController
 * @file ErrorsController.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Open\Errors\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\PageType;

final class ErrorsController extends OpenController
{

    private function _get_referer(): string
    {
        return $this->request->get_referer() ?? "vv";
    }

    public function notfound_404(): void
    {
        //to-do, check accept json por llamada ajax ya que si el router
        //no encuentra la url termina llegando a este método
        $back = __("Back");
        $this->add_header($code = ResponseType::NOT_FOUND)
            ->set_layout("open/mypromos/error")
            ->add_var(PageType::TITLE, $title = __("Error {0}!", $code))
            ->add_var(PageType::H1, $title)
            ->add_var("space", [])
            ->add_var("error", [
                __("Content not found"),
                ($urlback = $this->_get_referer()) ? "<a href=\"$urlback\">$back</a>" : "",
            ])
            ->add_var("code", $code)
            ->render_nv();
    }

    public function forbidden_403(): void
    {
        $this->set_layout("error/error")
            ->set_template("403")
            ->add_header(ResponseType::FORBIDDEN)
            ->add_var(PageType::TITLE, __("Forbidden"))
            ->add_var(PageType::H1, __("Forbidden"))
            ->add_var("urlback",$this->request->get_referer() ?? "")
            ->render();
    }

    public function internal_500(): void
    {
        $this->set_layout("error/error")
            ->set_template("500")
            ->add_header(ResponseType::INTERNAL_SERVER_ERROR)
            ->add_var(PageType::TITLE, __("Unexpected"))
            ->add_var(PageType::H1, __("Unexpected"))
            ->add_var("urlback",$this->request->get_referer() ?? "")
            ->render();
    }    

}//ErrorsController
