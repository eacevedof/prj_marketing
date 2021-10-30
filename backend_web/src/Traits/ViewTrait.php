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

    private function _init(): void
    {
        if(!$this->view) $this->view = new AppView();
    }

    protected function set_layout(string $pathlayout): AppView
    {
        $this->_init();
        $this->view->set_layout($pathlayout);
        return $this->view;
    }

    protected function add_var(string $varname, $value): AppView
    {
        $this->_init();
        $this->view->add_var($varname, $value);
        return $this->view;
    }

    protected function render(string $pathview=""): void
    {
        $this->_init();
        $this->view->set_vars($this->vars);
        $this->view->render($pathview);
    }

}//AppView
