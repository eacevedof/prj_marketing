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

use App\Enums\ResponseType;
use App\Enums\PageType;

final class ErrorsController extends OpenController
{
    public function error_404(): void
    {
        $this->set_layout("error/error")
            ->set_template("error/404")
            ->add_header(ResponseType::NOT_FOUND)
            ->add_var(PageType::TITLE, __("Content not found"))
            ->add_var(PageType::H1, __("Content not found"))
            ->add_var("urlback",$this->request->get_referer() ?? "")
            ->render();
    }

    public function forbidden(): void
    {
        $this->set_layout("error/error")
            ->set_template("error/403")
            ->add_header(ResponseType::FORBIDDEN)
            ->add_var(PageType::TITLE, __("Forbidden"))
            ->add_var(PageType::H1, __("Forbidden"))
            ->add_var("urlback",$this->request->get_referer() ?? "")
            ->render();
    }

    public function internal_500(): void
    {
        $this->set_layout("error/error")
            ->set_template("error/500")
            ->add_header(ResponseType::INTERNAL_SERVER_ERROR)
            ->add_var(PageType::TITLE, __("Unexpected"))
            ->add_var(PageType::H1, __("Unexpected"))
            ->add_var("urlback",$this->request->get_referer() ?? "")
            ->render();
    }    

}//ErrorsController
