<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Views\AppView
 * @file AppView.php 1.0.0
 * @date 30-10-2021 15:00 SPAIN
 * @observations
 * @tags: #apify
 */
namespace App\Views;

use App\Traits\LogTrait;
use \Exception;

final class AppView
{
    use LogTrait;

    private const PATH_LAYOUTS = PATH_SRC."/Views/layouts";
    private const PATH_TEMPLATES = PATH_SRC."/Views/templates";
    private const PATH_ELEMENTS = PATH_SRC."/Views/elements";

    private $request;

    private $vars = [];
    private $pathlayout = "";
    private $pathtemplate = "";

    public function __construct()
    {
        $this->request = $_REQUEST["ACTION"] ?? [];
    }

    private function _load_path_layout(): void
    {
        if (!$this->pathlayout) $this->pathlayout = self::PATH_LAYOUTS."/default.tpl";
    }

    private function _load_path_folder_template(): void
    {
        $strcontroller = $this->request["controller"] ?? "";
        if ($strcontroller) {
           $strcontroller = str_replace("App\Controllers","", $strcontroller);
           $strcontroller = str_replace("\\","/", $strcontroller);
           $strcontroller = strtolower($strcontroller);
           $strcontroller = str_replace("controller","", $strcontroller);
           $this->pathtemplate = self::PATH_TEMPLATES . "$strcontroller";
        }
    }

    private function _load_path_template_name(): void
    {
        if(!$this->pathtemplate) {
            $action = $this->request["action"] ?? "index";
            $this->pathtemplate .= "/$action.tpl";
        }
    }

    private function _template(): void
    {
       foreach ($this->vars as $name => $value)
            $$name = $value;

       include_once($this->pathtemplate);
    }

    private function _element(string $pathelement, $vars = []): void
    {
        $path = self::PATH_ELEMENTS."/$pathelement.tpl";
        if(!is_file($path)) $this->_exception("element $path does not exist!");

        foreach ($this->vars as $name => $value)
            $$name = $value;

        foreach ($vars as $name => $value)
            $$name = $value;

        include($path);
    }

    private function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }


    public function set_layout(string $pathlayout): AppView
    {
        if($pathlayout) $this->pathlayout = self::PATH_LAYOUTS ."/$pathlayout.tpl";
        return $this;
    }

    public function set_template(string $pattemplate): AppView
    {
        if(pathtemplate) $this->pathtemplate = self::PATH_TEMPLATES ."/$pattemplate.tpl";
        return $this;
    }

    public function render(): void
    {
        $this->_load_path_layout();
        if(!is_file($this->pathlayout)) $this->_exception("layout {$this->pathtemplate} not found");

        $this->_load_path_folder_template();
        $this->_load_path_template_name();
        if(!is_file($this->pathtemplate)) $this->_exception("template {$this->pathtemplate} not found");

        foreach ($this->vars as $name => $value)
            $$name = $value;

        include_once($this->pathlayout);
    }

    public function set_vars(array $vars): AppView
    {
        $this->vars = $vars;
        return $this;
    }

    public function add_var(string $name, $var): AppView
    {
        if(trim($name)!=="") $this->vars[$name] = $var;
        return $this;
    }

}//AppView
