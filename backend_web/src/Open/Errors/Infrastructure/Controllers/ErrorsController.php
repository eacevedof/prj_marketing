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

    private function _get_back_link(): string
    {
        $back = __("Back");
        return ($urlback = $this->request->get_referer()) ? "<a href=\"$urlback\" class=\"white\"><b>$back</b></a>" : "";
    }

    public function badrequest_400(): void
    {
        //$error = $this->_load_session()->get_once("global_error", []);
        $this->add_header($code = ResponseType::BAD_REQUEST)
            ->set_layout("open/tema/error")
            ->add_var(PageType::TITLE, $title = __("Bad request {0}!", $code))
            ->add_var(PageType::H1, $title)
            ->add_var("error", [
                __("Bad request"),
                $this->_get_back_link()
            ])
            ->add_var("code", $code)
            ->render_nv();
    }

    public function notfound_404(): void
    {
        $this->add_header($code = ResponseType::NOT_FOUND)
            ->set_layout("open/mypromos/error")
            ->add_var(PageType::TITLE, $title = __("Error {0}!", $code))
            ->add_var(PageType::H1, $title)
            ->add_var("space", [])
            ->add_var("error", [
                __("Content not found"),
                $this->_get_back_link()
            ])
            ->add_var("code", $code)
            ->render_nv();
    }

    public function forbidden_403(): void
    {
        $this->add_header($code = ResponseType::FORBIDDEN)
            ->set_layout("open/mypromos/error")
            ->add_var(PageType::TITLE, $title = __("Forbidden {0}!", $code))
            ->add_var(PageType::H1, $title)
            ->add_var("space", [])
            ->add_var("error", [
                __("You are not allowed to view this content"),
                $this->_get_back_link()
            ])
            ->add_var("code", $code)
            ->render_nv();
    }

    public function internal_500(): void
    {
        $this->add_header($code = ResponseType::FORBIDDEN)
            ->set_layout("open/mypromos/error")
            ->add_var(PageType::TITLE, $title = __("An unexpected error occurred!", $code))
            ->add_var(PageType::H1, $title)
            ->add_var("space", [])
            ->add_var("error", [
                __("Woops! Something went wrong."),
                __("Please, try again later"),
                $this->_get_back_link()
            ])
            ->add_var("code", $code)
            ->render_nv();
    }    

}//ErrorsController
