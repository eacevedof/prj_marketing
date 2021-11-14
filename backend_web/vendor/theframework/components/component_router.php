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
    private $arPieces;
    private $arArgs;

    public function __construct($arRoutes=[],$sPathRoutes="") 
    {
        $this->sRequestUri = $_SERVER["REQUEST_URI"];
        $this->sPathRoutes = $sPathRoutes;
        $this->arRoutes = $arRoutes;
        $this->arPieces = ["urlsep_exploded"=>[],"get_params"=>[]];
        $this->load_routes();
        $this->load_pieces();
        //print_r($this->sRequestUri);
        //print_r($this->arPieces);
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
        $arUrlsep = $this->get_urlsep_params($this->sRequestUri);
        $this->arPieces["urlsep_exploded"] = $arUrlsep;
        $this->arPieces["get_params"] = $arGet;
    }
    
    private function _search()
    {
        $isFound = false;
        foreach($this->arRoutes as $i=>$arRoute)
        {
            $sUrl = $arRoute["url"];
            $arRouteSep = $this->get_urlsep_params($sUrl);
            $this->arArgs = [];
            $isFound = $this->_compare_pieces($this->arPieces["urlsep_exploded"], $arRouteSep);
            if($isFound)
                break;
        }
        
        if($isFound)
            $this->add_to_get($this->arPieces["urlsep_exploded"],$arRouteSep);

        return array_merge(
            $this->arRoutes[$i],
            $this->arArgs ? ["args" => $this->arArgs] : []
        );
    }
    
    public function get_rundata()
    {
        return $this->_search();
    }
    
    private function _compare_pieces($arRequest, $arRoute)
    {
        //bug($arRequest);pr($arRoute);
        if(count($arRequest)!=count($arRoute))
            return false;
        
        foreach($arRoute as $i=>$sPiece)
        {
            if ($this->is_tag($sPiece)) {
                $arg = $this->get_tagkey($sPiece);
                $value = $arRequest[$i];
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
    
    private function unset_empties(&$arPieces)
    {
        $arNew = [];
        foreach($arPieces as $i=>$sValue)
            if($sValue)
                $arNew[] = $sValue;
        
        $arPieces = $arNew;
    }
    
    private function get_urlsep_params($sUrl)
    {
        $arTmp = explode("?",$sUrl);
        if(isset($arTmp[1])) $sUrl = $arTmp[0];
        $arPieces = explode("/",$sUrl);
        //pr($arPieces);
        $this->unset_empties($arPieces);
        return $arPieces;
    }    
    
}//ComponentRouter