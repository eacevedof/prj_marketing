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

namespace App\Shared\Infrastructure\Traits;

use App\Shared\Infrastructure\Views\AppView;

trait ViewTrait
{
    protected ?AppView $view = null;

    protected function _loadViewInstance(): AppView
    {
        if (!$this->view) {
            $this->view = new AppView;
        }
        return $this->view;
    }

    protected function setLayoutBySubPath(string $pathLayout): AppView
    {
        $this->_loadViewInstance()->setPartLayout($pathLayout);
        return $this->view;
    }

    protected function setTemplateBySubPath(string $pathTemplate): AppView
    {
        $this->_loadViewInstance()->setPartViewName($pathTemplate);
        return $this->view;
    }

    protected function addGlobalVar(string $varName, $value): AppView
    {
        $this->_loadViewInstance()->addGlobalVar($varName, $value);
        return $this->view;
    }

    protected function addHeaderCode(int $code): AppView
    {
        $this->_loadViewInstance()->addHeaderCode($code);
        return $this->view;
    }

    protected function render(array $vars = [], string $pathTemplate = ""): void
    {
        $this->_loadViewInstance();
        foreach ($vars as $varName => $varValue) {
            $this->view->addGlobalVar($varName, $varValue);
        }
        if ($pathTemplate) {
            $this->view->setPartViewName($pathTemplate);
        }
        $this->view->render();
    }

}
