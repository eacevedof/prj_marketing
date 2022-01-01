<?php

namespace App\Components\Request;

final class RequestComponent
{
    public function get_post($sKey=null)
    {
        if(!$sKey) return $_POST ?? [];
        return (isset($_POST[$sKey]) ? $_POST[$sKey] : null);
    }

    public function get_files($sKey=null)
    {
        if(!$sKey) return $_FILES ?? [];
        return (isset($_FILES[$sKey])?$_FILES[$sKey]:"");
    }

    public function get_get($sKey=null)
    {
        if(!$sKey) return $_GET ?? [];
        return (isset($_GET[$sKey])?$_GET[$sKey]:"");
    }

    public function is_post(){return count($_POST)>0;}

    public function is_get($sKey=null){if($sKey) return isset($_GET[$sKey]); return count($_GET)>0;}

    public function get_header($key=null)
    {
        $all = getallheaders();
        if(!$key) return $all;
        foreach ($all as $k=>$v)
            if(strtolower($k) === strtolower($key))
                return $v;
        return null;
    }
}