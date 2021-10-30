<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ViewTrait
 * @file ViewTrait.php 1.0.0
 * @date 30-10-2021 15:00 SPAIN
 * @observations
 * @tags: #ui
 */
namespace App\Traits;
use App\Views\AppView;

trait ViewTrait
{
    /**
     * @var AppView
     */
    private $view = null;
    private $vars = [];

    private function _init(): void
    {
        $this->view = new AppView();
    }

    protected function add_var(string $varname, $value): void
    {
        $this->vars[$varname] = $value;
    }

    protected function render(): void
    {
        $this->_init();
        $this->view->set_vars($this->vars);
        $this->view->render();
    }

}//AppView
