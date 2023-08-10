<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Infrastructure\Views\AppView
 * @file AppView.php 1.0.0
 * @date 30-10-2021 15:00 SPAIN
 * @observations
 * @tags: #apify
 */

namespace App\Shared\Infrastructure\Views;

use BOOT;
use Exception;
use App\Shared\Infrastructure\Traits\{DiskCacheTrait, LogTrait};

final class AppView
{
    use DiskCacheTrait;
    use LogTrait;

    private const PATH_LAYOUTS = BOOT::PATH_SRC."/Shared/Infrastructure/Views/layouts";
    private const PATH_ELEMENTS = BOOT::PATH_SRC."/Shared/Infrastructure/Views/elements";

    private const PATH_ASSETS_JS = "/assets/js/";
    private const PATH_ASSETS_IMG = "/assets/images/";
    private const PATH_ASSETS_CSS = "/assets/css/";

    private array $actionParts;
    private bool $doCache = false;
    private bool $useView = true;
    private string $requestUri;

    private array $globals = [];
    private array $locals = [];
    private array $httpHeaderCodes = [];

    private array $pathTplParts;

    //se necesita dentro del layout
    private string $pathTemplate = "";

    public function __construct()
    {
        $this->requestUri = $_SERVER["REQUEST_URI"] ?? "";
        $this->actionParts = $_REQUEST["APP_ACTION"] ?? [];
        $this->pathTplParts = [
            "layout" => self::PATH_LAYOUTS."/open/open.tpl",
            "viewfolder" => "",
            "viewname" => "",
        ];
        $this->_loadViewFolderFromController();
        $this->_loadViewNameFromMethod();
        $this->_loadDiskCacheInstance();
    }

    private function _throwException(string $message, int $code = 500): void
    {
        $this->logErr($message, "app-view.exception");
        throw new Exception($message, $code);
    }

    private function _loadViewFolderFromController(): void
    {
        if (!$strController = ($this->actionParts["controller"] ?? "")) {
            return;
        }

        $parts = explode("\\", $strController);
        unset($parts[0]);
        $parts = array_reverse($parts);
        unset($parts[0]);
        unset($parts[1]);
        $parts = array_reverse($parts);
        $parts[] = "Views";
        $strController = implode("/", $parts);
        $this->pathTplParts["viewfolder"] = BOOT::PATH_SRC."/$strController";
    }

    private function _loadViewNameFromMethod(): void
    {
        $actionMethod = $this->actionParts["method"] ?? "index";
        $this->pathTplParts["viewname"] = "$actionMethod.tpl";
    }

    private function _loadPathTemplate(): void
    {
        $this->pathTemplate = $this->pathTplParts["viewfolder"]."/".$this->pathTplParts["viewname"];
    }

    private function _template(): void
    {
        if (!$this->useView) {
            return;
        }

        if (!is_file($this->pathTemplate)) {
            $this->_throwException("template {$this->pathTemplate} does not exist!");
        }

        foreach ($this->globals as $name => $value) {
            $$name = $value;
        }

        foreach ($this->locals as $name => $value) {
            $$name = $value;
        }

        include_once($this->pathTemplate);
    }

    private function _element(string $pathElement, array $vars = []): void
    {
        $path = self::PATH_ELEMENTS."/$pathElement.tpl";
        if (!is_file($path)) {
            $this->_throwException("element $path does not exist!");
        }

        foreach ($this->globals as $name => $value) {
            $$name = $value;
        }

        foreach ($vars as $name => $value) {
            $$name = $value;
        }

        include($path);
    }

    private function _includeViewElement(string $subPathElement, array $vars = []): void
    {
        $pathTpl = "{$this->pathTplParts["viewfolder"]}/elements/$subPathElement.tpl";
        if (!is_file($pathTpl)) {
            $this->_throwException("element $pathTpl does not exist!");
        }

        foreach ($this->globals as $name => $value) {
            $$name = $value;
        }

        foreach ($vars as $name => $value) {
            $$name = $value;
        }

        include($pathTpl);
    }

    private function _getAssetJsTagAsModule(string|array $pathJs): string
    {
        return $this->_getAssetJsTag($pathJs, "module");
    }

    private function _getAssetJsTag(string|array $pathJs, $type = ""): string
    {
        $type = $type ? " type=\"$type\"" : " ";

        if (is_string($pathJs)) {
            $path = self::PATH_ASSETS_JS.$pathJs.".js";
            $fc = substr($pathJs, 0, 1);
            if ($fc === "/") {
                $path = $pathJs.".js";
            }
            return "<script{$type}src=\"$path\"></script>";
        }

        if (is_array($pathJs)) {
            $html = [];
            foreach ($pathJs as $path_js) {
                $path = self::PATH_ASSETS_JS.$path_js.".js";
                $fc = substr($path_js, 0, 1);
                if ($fc === "/") {
                    $path = $path_js.".js";
                }
                $html[] = "<script{$type}src=\"$path\"></script>";
            }
            return implode("\n", $html);
        }
        return "";
    }

    private function _getAssetCssTag(string | array $pathCss): string
    {
        if (is_string($pathCss)) {
            $path = self::PATH_ASSETS_CSS . $pathCss . ".css";
            $fc = substr($pathCss, 0, 1);
            if ($fc === "/") {
                $path = $pathCss.".css";
            }
            return "<link href=\"$path\" rel=\"stylesheet\">";
        }

        if (is_array($pathCss)) {
            $html = [];
            foreach ($pathCss as $path_css) {
                $path = self::PATH_ASSETS_CSS . $path_css . ".css";
                $fc = substr($path_css, 0, 1);
                if ($fc === "/") {
                    $path = $path_css.".css";
                }
                $html[] = "<link href=\"$path\" rel=\"stylesheet\">";
            }
            return implode("\n", $html);
        }
        return "";
    }

    private function _getAssetImgPath(string $pathImg): string
    {
        $path = self::PATH_ASSETS_IMG.$pathImg;
        return $path;
    }

    private function _cacheExit(): void
    {
        if ($this->doCache && $this->diskCacheComponent->isCachedFileAlive()) {
            $content = $this->diskCacheComponent->getCachedFileContent();
            $this->_sendResponseHeaders();
            exit($content);
        }
    }

    private function _echoJs(mixed $any): void
    {
        $json = json_encode($any, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json;
    }

    private function _echoJsLit(mixed $any): void
    {
        $json = json_encode($any, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo str_replace("\"", "&quot;", $json);
    }

    private function _echo(?string $any, bool $raw = true): void
    {
        $any = ($any ?? "");
        echo $raw ? $any : htmlentities($any);
    }

    private function _echoHtmlEscaped(?string $any): void
    {
        echo htmlentities($any);
    }

    private function _getHtmlEscaped(?string $any): string
    {
        return htmlentities($any);
    }

    private function _sendResponseHeaders(): void
    {
        $headers = array_unique($this->httpHeaderCodes);
        foreach ($headers as $code) {
            http_response_code($code);
        }
    }

    private function _flushAndExit(): void
    {
        $this->_sendResponseHeaders();
        if ($this->doCache) {
            $content = ob_get_contents();
            $now = date("Y-m-d H:i:s");
            $content .= "<!-- cached at $now -->";
            $this->diskCacheComponent->write($content);
            exit($content);
        }
        //$isflushok = ob_end_flush();
        die();
    }

    public function setPartLayout(string $pathLayout): self
    {
        $this->pathTplParts["layout"] = self::PATH_LAYOUTS ."/$pathLayout.tpl";
        return $this;
    }

    public function setPartViewFolder(string $folder): self
    {
        $this->pathTplParts["viewfolder"] = BOOT::PATH_SRC."/$folder";
        return $this;
    }

    public function setPartViewName(string $viewName): self
    {
        $this->pathTplParts["viewname"] = "$viewName.tpl";
        return $this;
    }

    public function cache(int $time = 3600, string $folder = ""): self
    {
        if (!$time) {
            $this->doCache = false;
            return $this;
        }
        $this->doCache = true;
        $this->diskCacheComponent
            ->setKeyToBeHashed($this->requestUri)->setSecondsTtl($time)->setSubFolder($folder);
        return $this;
    }

    public function addGlobalVar(string $name, mixed $var): self
    {
        if(trim($name) !== "") {
            $this->globals[$name] = $var;
        }
        return $this;
    }

    public function addHeaderCode(int $code): self
    {
        $this->httpHeaderCodes[] = $code;
        return $this;
    }

    public function render(array $vars = []): void
    {
        $this->_cacheExit();
        $this->locals = $vars;

        if (!is_file($this->pathTplParts["layout"])) {
            $this->_throwException("layout {$this->pathTplParts["layout"]} not found");
        }

        $this->_loadPathTemplate();
        if (!is_file($this->pathTemplate)) {
            $this->_throwException("template {$this->pathTemplate} not found");
        }

        foreach ($this->globals as $name => $value) {
            $$name = $value;
        }

        foreach ($this->locals as $name => $value) {
            $$name = $value;
        }

        include_once($this->pathTplParts["layout"]);

        $this->_flushAndExit();
    }

    public function renderLayoutOnly(array $vars = []): void
    {
        $this->useView = false;
        $this->_cacheExit();
        $this->locals = $vars;

        if (!is_file($this->pathTplParts["layout"])) {
            $this->_throwException("layout {$this->pathTplParts["layout"]} not found");
        }

        foreach ($this->globals as $name => $value) {
            $$name = $value;
        }

        foreach ($this->locals as $name => $value) {
            $$name = $value;
        }

        include_once($this->pathTplParts["layout"]);

        $this->_flushAndExit();
    }

    public function renderViewOnly(array $vars = []): void
    {
        $this->_cacheExit();
        $this->locals = $vars;

        $this->_loadPathTemplate();
        if (!is_file($this->pathTemplate)) {
            $this->_throwException("template {$this->pathTemplate} not found");
        }

        foreach ($this->globals as $name => $value) {
            $$name = $value;
        }

        foreach ($this->locals as $name => $value) {
            $$name = $value;
        }

        include_once($this->pathTemplate);
        $this->_flushAndExit();
    }

}//AppView
