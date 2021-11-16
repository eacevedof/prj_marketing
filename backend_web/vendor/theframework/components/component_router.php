<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name TheApplication\Components\ComponentRouter 
 * @file ComponentRouter.php 1.0.0
 * @date 28-06-2019 08:19 SPAIN
 * @observations
 */
namespace TheFramework\Components;

final class ComponentRouter
{   
    private $sRequestUri;
    private $sPathRoutes;
    private $arRoutes;
    private $arRequest;
    private $arArgs;

    public function __construct($arRoutes=[],$sPathRoutes="") 
    {
        $this->sRequestUri = $_SERVER["REQUEST_URI"];
        $this->sPathRoutes = $sPathRoutes;
        $this->arRoutes = $arRoutes;
        $this->arRequest = ["url"=>"","url_pieces"=>[],"get_params"=>[]];
        $this->load_routes();
        $this->load_pieces();
        //print_r($this->sRequestUri);
        //print_r($this->arRequest);
    }
    
    private function load_routes()
    {
        if(!$this->arRoutes)
        {
            if($this->sPathRoutes)
            {
                $this->arRoutes = include($this->sPathRoutes);
            }
        }
    }

    private function load_pieces()
    {
        $arGet = $this->get_get_params($this->sRequestUri);
        $arUrlsep = $this->_get_url_pieces($this->sRequestUri);
        $this->arRequest["url"] = "/".implode("/",$arUrlsep);
        $this->arRequest["url_pieces"] = $arUrlsep;
        $this->arRequest["get_params"] = $arGet;
    }

    private function _search_exact(): array
    {
        $requri = $this->arRequest["url"];
        $routes = array_filter($this->arRoutes, function ($route) use ($requri) {
            return $route["url"] === $requri;
        });
        return reset($routes) ?: [];
    }

    private function _search_by_pieces(): array
    {
        $isFound = false;
        foreach($this->arRoutes as $i=>$arRoute)
        {
            $sUrl = $arRoute["url"];
            $arRouteSep = $this->_get_url_pieces($sUrl);
            $this->arArgs = [];
            $isFound = $this->_compare_pieces($this->arRequest["url_pieces"], $arRouteSep);
            if($isFound)
                break;
        }
        
        if($isFound)
            $this->add_to_get($this->arRequest["url_pieces"], $arRouteSep);

        return array_merge(
            $this->arRoutes[$i],
            $this->arArgs ? ["_args" => $this->arArgs] : []
        );
    }
    
    public function get_rundata(): array
    {
        return $this->_search_exact() ?: $this->_search_by_pieces();
    }

    private function _is_nullable(array $route): bool
    {
        $null = array_filter(
            array_values($route),
            function ($string) {
                return strstr($string, "?:");
            }
        );
        return (bool) count($null);
    }

    private function _is_probable(array $request, array $route): bool
    {
        if (($ireq = count($request))===($iroute = count($route)))
            return true;

        //esto no está del todo fino ya que no se permitiria varios ?: como partes de la ruta
        if($this->_is_nullable($route) && $ireq===($iroute-1))
            return true;

        return false;
    }

    private function _compare_pieces($arRequest, $arRoute)
    {
        if(!$this->_is_probable($arRequest, $arRoute))
            return false;
        
        foreach($arRoute as $i=>$sPiece)
        {
            if ($this->is_tag($sPiece)) {
                $arg = $this->get_tagkey($sPiece);
                $value = $arRequest[$i] ?? null;
                $this->arArgs[$arg] = $value;
                continue;
            }
            $sReqval = $arRequest[$i];
            if($sReqval != $sPiece)
                return false;
        }
        return true;
    }
    
    private function add_to_get($arRequest,$arRoute)
    {
        foreach($arRoute as $i=>$sPiece)
        {
            if(!$this->is_tag($sPiece))
                continue;
            $sKey = $this->get_tagkey($sPiece);
            $_GET[$sKey] = $arRequest[$i];
        }
    }
    
    private function is_tag($sPiece)
    {
        return (
            (strstr($sPiece,"{") && strstr($sPiece,"}")) ||
            strstr($sPiece,":") ||
            strstr($sPiece,"?:")
        );
    }
    
    private function get_tagkey($sPiece){return str_replace(["{","}","?:",":"],"",$sPiece);}
    
    private function explode_and($sAndstring)
    {
        $arRet = [];
        $arTmp = explode("&",$sAndstring);
        foreach($arTmp as $sEq)
        {
            $arParamVal = explode("=",$sEq);
            $arRet[$arParamVal[0]] = isset($arParamVal[1])?$arParamVal[1]:"";
        }
        return $arRet;
    }
    
    private function get_get_params($sUrl)
    {
        $arTmp = explode("?",$sUrl);
        if(!isset($arTmp[1])) return [];
        $arParams = $this->explode_and($arTmp[1]);
        return $arParams;
    }
    
    private function unset_empties(&$arRequest)
    {
        $arNew = [];
        foreach($arRequest as $i=>$sValue)
            if($sValue)
                $arNew[] = $sValue;
        
        $arRequest = $arNew;
    }
    
    private function _get_url_pieces($sUrl): array
    {
        $arTmp = explode("?",$sUrl);
        if(isset($arTmp[1])) $sUrl = $arTmp[0];
        $arRequest = explode("/",$sUrl);
        //pr($arRequest);
        $this->unset_empties($arRequest);
        return $arRequest;
    }    
    
}//ComponentRouter