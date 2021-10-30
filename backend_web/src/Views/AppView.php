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

    private const PATH_LAYOUTS = PATH_SRC."/Views/layouts";
    private const PATH_VIEWS = PATH_SRC."/Views";
    private const PATH_ELEMENTS = PATH_SRC."/Views/elements";

    private $request;
    
    private $pathlayout = "";
    private $pathtemplate = "";
    private $vars = [];

    public function __construct()
    {
        $this->request = $_REQUEST["ACTION"] ?? [];
    }

    private function _load_path_folder_view(): void
    {
        $strcontroller = $this->request["controller"] ?? "";
        if ($strcontroller) {
           $strcontroller = str_replace("App\Controllers","", $strcontroller);
           $strcontroller = str_replace("\\","/", $strcontroller);
           $strcontroller = strtolower($strcontroller);
           $strcontroller = str_replace("controller","", $strcontroller);
           $this->pathview = self::PATH_VIEWS . "/$strcontroller";
        }
    }

    private function _load_path_view_name(): void
    {
        if($this->pathview) {
            $action = $this->request["action"] ?? "index";
            $this->pathview .= "";
        }
    }

    public function set_layout(string $pathlayout): AppView
    {
        if($pathlayout) $this->pathlayout = self::PATH_LAYOUTS ."/$pathlayout";
        return $this;
    }

    public function render(string $pathtemplate=""): void
    {
        $this->_load_path_folder_view();
        $this->_load_path_view_name();

        if(!is_file($this->pathview))  throw new \Exception("view {$this->pathview} not found");

        foreach ($this->vars as $name => $value)
            $$name = $value;
        var_dump($this->vars);
        if ($this->pathview) include($this->pathview);
        die("rendred");
    }

    public function set_vars(array $vars): void
    {
        $this->vars = $vars;
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
