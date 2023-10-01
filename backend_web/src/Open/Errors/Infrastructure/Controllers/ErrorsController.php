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

use App\Shared\Domain\Enums\{PageType, ResponseType};
use App\Shared\Infrastructure\Controllers\Open\OpenController;

final class ErrorsController extends OpenController
{
    private function _getBackLink(): string
    {
        $back = __("Back");
        return ($urlback = $this->requestComponent->getReferer()) ? "<a href=\"$urlback\" class=\"white\"><b>$back</b></a>" : "";
    }

    public function notFound404(): void
    {
        //todo, check accept json por llamada ajax ya que si el router
        //no encuentra la url termina llegando a este método

        $this->addHeaderCode($code = ResponseType::NOT_FOUND)
            ->setPartLayout("open/mypromos/error")
            ->addGlobalVar(PageType::TITLE, $title = __("Error {0}!", $code))
            ->addGlobalVar(PageType::H1, $title)
            ->addGlobalVar("space", [])
            ->addGlobalVar("error", [
                __("Content not found"),
                $this->_getBackLink()
            ])
            ->addGlobalVar("code", $code)
            ->renderLayoutOnly();
    }

    public function forbidden403(): void
    {
        $this->addHeaderCode($code = ResponseType::FORBIDDEN)
            ->setPartLayout("open/mypromos/error")
            ->addGlobalVar(PageType::TITLE, $title = __("Forbidden {0}!", $code))
            ->addGlobalVar(PageType::H1, $title)
            ->addGlobalVar("space", [])
            ->addGlobalVar("error", [
                __("You are not allowed to view this content"),
                $this->_getBackLink()
            ])
            ->addGlobalVar("code", $code)
            ->renderLayoutOnly();
    }

    public function internal500(): void
    {
        $this->addHeaderCode($code = ResponseType::FORBIDDEN)
            ->setPartLayout("open/mypromos/error")
            ->addGlobalVar(PageType::TITLE, $title = __("An unexpected error occurred!", $code))
            ->addGlobalVar(PageType::H1, $title)
            ->addGlobalVar("space", [])
            ->addGlobalVar("error", [
                __("Woops! Something went wrong."),
                __("Please, try again later"),
                $this->_getBackLink()
            ])
            ->addGlobalVar("code", $code)
            ->renderLayoutOnly();
    }

}//ErrorsController
