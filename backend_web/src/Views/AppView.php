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

use App\Traits\ErrorTrait;
use App\Traits\LogTrait;
use App\Traits\EnvTrait;
use \Exception;

final class AppView
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;

    //private const PATH_VIEWS = PATH_SRC."/Views";
    private const PATH_LAYOUTS = PATH_SRC."/Views/layouts";
    private const PATH_TEMPLATES = PATH_SRC."/Views/templates";
    private const PATH_ELEMENTS = PATH_SRC."/Views/elements";

    private $request;
    
    private $pathlayout = "";
    private $pathtemplate = "";
    private $vars = [];

    public function __construct()
    {
        $this->request = $_REQUEST["ACTION"] ?? [];
    }

    private function _load_path_layout(): void
    {
        $this->pathlayout = self::PATH_LAYOUTS."/default.php";
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
        if($this->pathtemplate) {
            $action = $this->request["action"] ?? "index";
            $this->pathtemplate .= "/$action.php";
        }
    }

    public function set_layout(string $pathlayout): AppView
    {
        if($pathlayout) $this->pathlayout = self::PATH_LAYOUTS ."/$pathlayout.php";
        return $this;
    }

    public function render(string $pathtemplate=""): void
    {
        $this->_load_path_layout();
        if(!is_file($this->pathlayout)) throw new \Exception("layout {$this->pathtemplate} not found");

        $this->_load_path_folder_template();
        $this->_load_path_template_name();
        if(!is_file($this->pathtemplate)) throw new \Exception("template {$this->pathtemplate} not found");

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

    public function template(): void
    {
       foreach ($this->vars as $name => $value)
            $$name = $value;

       include_once($this->pathtemplate);
    }


    public function element(string $pathelement, $vars = []): void
    {
        $path = self::PATH_ELEMENTS."/$pathelement.php";
        if(!is_file($path))
            throw new \Exception("element $path does not exist!");

        foreach ($this->vars as $name => $value)
            $$name = $value;

        foreach ($vars as $name => $value)
            $$name = $value;

        include($path);
    }

    protected function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }
}//AppView
