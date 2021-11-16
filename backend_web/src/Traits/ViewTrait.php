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
    private ?AppView $view = null;

    private function _viewinit(): void
    {
        if(!$this->view) $this->view = new AppView();
    }

    protected function set_layout(string $pathlayout): AppView
    {
        $this->_viewinit();
        $this->view->set_layout($pathlayout);
        return $this->view;
    }

    protected function add_var(string $varname, $value): AppView
    {
        $this->_viewinit();
        $this->view->add_var($varname, $value);
        return $this->view;
    }

    protected function render($vars=[], string $pathtemplate=""): void
    {
        $this->_viewinit();
        foreach ($vars as $k => $v)
            $this->view->add_var($k,$v);
        if($pathtemplate) $this->view->set_template($pathtemplate);
        $this->view->render();
    }

    protected function render_nl($vars=[], string $pathtemplate=""): void
    {
        $this->_viewinit();
        foreach ($vars as $k => $v)
            $this->view->add_var($k,$v);
        if($pathtemplate) $this->view->set_template($pathtemplate);
        $this->view->render_nl();
    }

    protected function render_error($vars=[], string $pathtemplate=""): void
    {
        $this->_viewinit();
        foreach ($vars as $k => $v)
            $this->view->add_var($k,$v);
        if($pathtemplate) $this->view->set_template($pathtemplate);
        $this->view->render();
        exit();
    }

}//ViewTrait
