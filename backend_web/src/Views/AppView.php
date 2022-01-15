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

use App\Traits\DiskCacheTrait;
use App\Traits\LogTrait;
use \Exception;

final class AppView
{
    use LogTrait;
    use DiskCacheTrait;

    private const PATH_LAYOUTS = PATH_SRC."/Views/layouts";
    private const PATH_TEMPLATES = PATH_SRC."/Views/templates";
    private const PATH_ELEMENTS = PATH_SRC."/Views/elements";

    private const PATH_ASSETS_JS = "/assets/js/";
    private const PATH_ASSETS_IMG = "/assets/images/";
    private const PATH_ASSETS_CSS = "/assets/css/";

    private array $araction;
    private bool $docache = false;
    private string $requri;

    private array $globals = [];
    private array $locals = [];
    private array $headers = [];

    private string $pathlayout = "";
    private string $pathtemplate = "";

    public function __construct()
    {
        $this->requri = $_SERVER["REQUEST_URI"];
        $this->araction = $_REQUEST["ACTION"] ?? [];
        $this->_load_diskcache();
    }

    private function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"app-view.exception");
        throw new Exception($message, $code);
    }
    
    private function _load_path_layout(): void
    {
        if (!$this->pathlayout) $this->pathlayout = self::PATH_LAYOUTS."/default.tpl";
    }

    private function _load_path_folder_template(): void
    {
        $strcontroller = $this->araction["controller"] ?? "";
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
        $araction = $this->araction["method"] ?? "index";
        $this->pathtemplate .= "/$araction.tpl";
    }

    private function _template(): void
    {
       foreach ($this->globals as $name => $value)
            $$name = $value;

        foreach ($this->locals as $name => $value)
            $$name = $value;

       include_once($this->pathtemplate);
    }

    private function _element(string $pathelement, $vars = []): void
    {
        $path = self::PATH_ELEMENTS."/$pathelement.tpl";
        if(!is_file($path)) $this->_exception("element $path does not exist!");

        foreach ($this->globals as $name => $value)
            $$name = $value;

        foreach ($vars as $name => $value)
            $$name = $value;

        include($path);
    }

    private function _asset_js_module($pathjs):string
    {
        return $this->_asset_js($pathjs, "module");
    }

    private function _asset_js($pathjs, $type=""):string
    {
        $type = $type ? " type=\"$type\"" : " ";

        if (is_string($pathjs)) {
            $path = self::PATH_ASSETS_JS.$pathjs.".js";
            return "<script{$type}src=\"$path\"></script>";
        }

        if (is_array($pathjs)) {
            $html = [];
            foreach ($pathjs as $path) {
                $path = self::PATH_ASSETS_JS.$path.".js";
                $html[] = "<script{$type}src=\"$path\"></script>";
            }
            return implode("\n",$html);
        }
        return "";
    }

    private function _asset_css($pathcss):string
    {
        if (is_string($pathcss)) {
            $path = self::PATH_ASSETS_CSS . $pathcss . ".css";
            return "<link href=\"$path\" rel=\"stylesheet\">";
        }

        if (is_array($pathcss)) {
            $html = [];
            foreach ($pathcss as $path) {
                $path = self::PATH_ASSETS_CSS . $path . ".css";
                $html[] = "<link href=\"$path\" rel=\"stylesheet\">";
            }
            return implode("\n",$html);
        }
        return "";
    }

    private function _asset_img(string $pathimg):string
    {
        $path = self::PATH_ASSETS_IMG.$pathimg;
        return $path;
    }
    
    private function _cache_exit(): void
    {
        if ($this->docache && $this->diskcache->is_alive()) {
            $content = $this->diskcache->get_content();
            $this->_send_headers();
            exit($content);
        }   
    }
    
    private function _echo_js($any): void
    {
        $json = json_encode($any);
        echo $json;
    }

    private function _echo_jslit($any): void
    {
        $json = json_encode($any);
        echo str_replace("\"","&quot;", $json);
    }

    private function _echo(?string $any, bool $raw=true): void
    {
        $any = ($any ?? "");
        echo $raw ? $any : htmlentities($any);
    }
    
    private function _send_headers(): void
    {
        $headers = array_unique($this->headers);
        foreach ($headers as $code)
            http_response_code($code);
    }

    private function _flush_and_exit(): void
    {
        $this->_send_headers();
        if ($this->docache) {
            $content = ob_get_contents();
            $now = date("Y-m-d H:i:s");
            $content .= "<!-- cached at $now -->";
            $this->diskcache->write($content);
            exit($content);
        }
        $isflushok = ob_end_flush();
        exit();
    }

    public function set_layout(string $pathlayout): self
    {
        if($pathlayout) $this->pathlayout = self::PATH_LAYOUTS ."/$pathlayout.tpl";
        return $this;
    }

    public function set_template(string $pathtemplate): self
    {
        if($pathtemplate) $this->pathtemplate = self::PATH_TEMPLATES ."/$pathtemplate.tpl";
        return $this;
    }

    public function cache(int $time=3600, string $folder=""): self
    {
        if (!$time) {
            $this->docache = false;
            return $this;
        }
        $this->docache = true;
        $this->diskcache
            ->set_keyname($this->requri)->set_time($time)->set_folder($folder);
        return $this;
    }

    public function add_var(string $name, $var): self
    {
        if(trim($name)!=="") $this->globals[$name] = $var;
        return $this;
    }

    public function add_header(int $code): self
    {
        $this->headers[] = $code;
        return $this;
    }

    public function render(array $vars = []): void
    {
        $this->_cache_exit();
        $this->locals = $vars;

        $this->_load_path_layout();
        if(!is_file($this->pathlayout)) $this->_exception("layout {$this->pathtemplate} not found");

        if (!$this->pathtemplate) {
            $this->_load_path_folder_template();
            $this->_load_path_template_name();
        }

        if(!is_file($this->pathtemplate)) $this->_exception("template {$this->pathtemplate} not found");

        //esto publica para el layout más no para el template ya que
        //el $this->_template() no ve estas variables y se llama dentro de pathlayout
        foreach ($this->globals as $name => $value)
            $$name = $value;

        foreach ($this->locals as $name => $value)
            $$name = $value;

        include_once($this->pathlayout);

        $this->_flush_and_exit();
    }

    public function render_nl(array $vars = []): void
    {
        $this->_cache_exit();
        $this->locals = $vars;
        if (!$this->pathtemplate) {
            $this->_load_path_folder_template();
            $this->_load_path_template_name();
        }

        if(!is_file($this->pathtemplate)) $this->_exception("template {$this->pathtemplate} not found");

        foreach ($this->globals as $name => $value)
            $$name = $value;

        foreach ($this->locals as $name => $value)
            $$name = $value;

        include_once($this->pathtemplate);
        $this->_flush_and_exit();
    }
    
    
}//AppView
